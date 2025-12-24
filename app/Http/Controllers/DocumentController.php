<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DocumentController extends Controller
{
    /**
     * 📥 Inbox / รายการเอกสาร
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Base Query + Relations
        |--------------------------------------------------------------------------
        */
        $query = Document::query()
            ->with('department');

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Role-based visibility
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'user') {
            if ($request->boolean('mine')) {
                $query->where('created_by', $user->id);
            }
        } elseif ($user->role === 'clerk') {
            $query->whereIn('status', [
                'received',
                'waiting_director',
                'approved',
                'rejected',
                'distributed',
                'cancelled',
            ]);
        } elseif ($user->role === 'director') {
            $query->whereIn('status', [
                'waiting_director',
                'approved',
            ]);
        }
        // admin → เห็นทั้งหมด

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('doc_no', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Filters
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ Pagination
        |--------------------------------------------------------------------------
        */
        $documents = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ Summary
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'user') {
            $summary = [
                'my_documents' => Document::where('created_by', $user->id)->count(),
                'pending' => Document::where('created_by', $user->id)
                    ->whereNotIn('status', ['approved', 'rejected', 'distributed'])
                    ->count(),
                'approved' => Document::where('created_by', $user->id)
                    ->where('status', 'approved')
                    ->count(),
                'rejected' => Document::where('created_by', $user->id)
                    ->where('status', 'rejected')
                    ->count(),
            ];
        } else {
            $summary = [
                'my_documents' => Document::count(),
                'pending' => Document::whereIn('status', [
                    'received',
                    'waiting_director',
                ])->count(),
                'approved' => Document::where('status', 'approved')->count(),
                'rejected' => Document::where('status', 'rejected')->count(),
                'cancelled' => Document::where('status', 'cancelled')->count(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 7️⃣ Render (ครั้งเดียวเท่านั้น)
        |--------------------------------------------------------------------------
        */
        return Inertia::render('Documents/Index', [
            'documents'   => $documents,
            'summary'     => $summary,
            'statuses'    => Document::STATUS_LABELS,
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'filters'     => $request->only([
                'search',
                'status',
                'department',
                'mine',
            ]),
        ]);
    }

    /**
     * ➕ หน้า Create เอกสาร
     */
    public function create()
    {
        return Inertia::render('Documents/Create', [
            'departments' => Department::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * 💾 สร้างเอกสาร (Draft)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        $document = Document::create([
            'title' => $data['title'],
            'department_id' => $data['department_id'],
            'created_by' => Auth::id(),
            'status' => 'draft',
        ]);

        $document->logs()->create([
            'user_id'     => Auth::id(),
            'action'      => 'created',
            'from_status' => null,
            'to_status'   => 'draft',
            'remark'      => 'สร้างเอกสาร',
        ]);

        return redirect()
            ->route('documents.index')
            ->with('success', 'สร้างเอกสารเรียบร้อยแล้ว');
    }

    /**
     * 👁 แสดงเอกสาร
     */
    public function show(Document $document)
    {
        $document->load([
            'logs.user',
            'attachments',
            'department',
        ]);

        return Inertia::render('Documents/Show', [
            'document'  => $document,
            'canCancel' => $document->canBeCancelled(),
            'auth' => [
                'user' => auth()->user(),
            ],
        ]);
    }
}
