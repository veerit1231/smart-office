<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * รายงานเอกสาร (แยกตามประเภท)
     */
    public function documents(Request $request)
    {
        // ===============================
        // รับค่า filter
        // ===============================
        $filters = [
            'type'       => $request->input('type'),        // incoming | internal
            'department' => $request->input('department'),  // department_id
            'status'     => $request->input('status'),
            'from'       => $request->input('from'),
            'to'         => $request->input('to'),
        ];

        // ===============================
        // Query หลัก
        // ===============================
        $query = Document::query()
            ->with('department');

        if ($filters['type']) {
            $query->where('type', $filters['type']);
        }

        if ($filters['department']) {
            $query->where('department_id', $filters['department']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['from'] && $filters['to']) {
            $query->whereBetween('created_at', [
                $filters['from'] . ' 00:00:00',
                $filters['to'] . ' 23:59:59',
            ]);
        }

        // ===============================
        // ดึงข้อมูล
        // ===============================
        $documents = $query
            ->latest()
            ->get();

        // ===============================
        // Summary (ตัวเลขสรุป)
        // ===============================
        $summary = [
            'total'     => $documents->count(),
            'incoming'  => $documents->where('type', 'incoming')->count(),
            'internal'  => $documents->where('type', 'internal')->count(),
            'approved'  => $documents->where('status', 'approved')->count(),
            'rejected'  => $documents->where('status', 'rejected')->count(),
            'waiting'   => $documents->whereIn('status', [
                'waiting_director',
                'received',
            ])->count(),
        ];

        // ===============================
        // ส่งไป Inertia
        // ===============================
        return Inertia::render('Reports/DocumentsReport', [
            'documents'   => $documents,
            'summary'     => $summary,
            'filters'     => $filters,
            'departments' => Department::select('id', 'name')->get(),
        ]);
    }
}
