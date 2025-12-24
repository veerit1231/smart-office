<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentLog;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExternalDocumentController extends Controller
{
    /**
     * 📥 หน้าเอกสารรับภายนอก (Incoming Index)
     * clerk เท่านั้น
     */
    public function index()
    {
        abort_unless(Auth::user()->role === 'clerk', 403);

        $documents = Document::where('type', 'incoming')
    ->with('department') // ⭐ เพิ่มบรรทัดนี้
    ->latest()
    ->paginate(10);

        // หน่วยงานต้นทาง (external only)
        $departments = Department::orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Documents/Incoming/Index', [
            'documents'   => $documents,
            'departments' => $departments,
        ]);
    }

    /**
     * 💾 รับเอกสารภายนอกเข้าระบบ
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->role === 'clerk', 403);

        $data = $request->validate([
            'file'          => 'required|file|mimes:pdf|max:10240',
            'department_id' => 'required|exists:departments,id', // หน่วยงานต้นทาง
            'title'         => 'required|string|max:255',
            'doc_date'      => 'required|date',
            'received_date' => 'required|date',
            'purpose'       => 'required|in:inform,consider,notify',
            'notify_to'     => 'nullable|string|max:255',
            'urgent'        => 'boolean',
            'use_esign'     => 'boolean',
        ]);

        // 📎 เก็บไฟล์
        $path = $request->file('file')->store(
            'documents/incoming',
            'public'
        );

        // 🔢 เลขรับอัตโนมัติ
        $docNo = Document::generateIncomingNumber();

        // 📄 สร้างเอกสารรับนอก
        $document = Document::create([
            'doc_no'        => $docNo,
            'title'         => $data['title'],
            'type'          => 'incoming',
            'status'        => 'received',
            // 'current_step' => 'clerk', // ใช้เฉพาะถ้า workflow ต้องการจริง
            'created_by'    => Auth::id(),
            'department_id' => $data['department_id'], // ⭐ หน่วยงานต้นทาง
            'file_path'     => $path,
            'meta' => [
                'doc_date'      => $data['doc_date'],
                'received_date' => $data['received_date'],
                'purpose'       => $data['purpose'],
                'notify_to'     => $data['notify_to'] ?? null,
                'urgent'        => $data['urgent'] ?? false,
                'use_esign'     => $data['use_esign'] ?? true,
            ],
        ]);

        // 🧾 log การรับเอกสาร
        DocumentLog::create([
            'document_id' => $document->id,
            'user_id'     => Auth::id(),
            'action'      => 'received',
            'from_status' => null,
            'to_status'   => 'received',
            'remark'      => 'รับเอกสารภายนอกเข้าระบบ',
        ]);

        return back()->with('success', 'รับเอกสารภายนอกเรียบร้อยแล้ว');
    }
}
