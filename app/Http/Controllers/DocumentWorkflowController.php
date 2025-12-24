<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentLog;
use App\Models\DocumentDistribution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\DocumentApprovedNotification;
use App\Notifications\DocumentDistributedNotification;
use App\Models\DocumentCounter;
use App\Models\DocumentTimeline;

class DocumentWorkflowController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Status constants
    |--------------------------------------------------------------------------
    */
    private const STATUS_DRAFT             = 'draft';
    private const STATUS_RECEIVED          = 'received';
    private const STATUS_WAITING_DIRECTOR  = 'waiting_director';
    private const STATUS_APPROVED          = 'approved';
    private const STATUS_REJECTED          = 'rejected';
    private const STATUS_DISTRIBUTED       = 'distributed';

    /*
    |--------------------------------------------------------------------------
    | Step constants
    |--------------------------------------------------------------------------
    */
    private const STEP_CLERK    = 'clerk';
    private const STEP_DIRECTOR = 'director';

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ USER ส่งเอกสาร (draft → clerk)
    |--------------------------------------------------------------------------
    */
    public function submitFromUser(Request $request, Document $document)
{
    $this->authorizeRole(['user', 'admin']);

    $request->validate([
        'remark' => 'nullable|string|max:255',
    ]);

    DB::transaction(function () use ($document, $request) {
        $document->refresh();

        abort_unless(
            $document->status === self::STATUS_DRAFT &&
            (
                $document->created_by === Auth::id() ||
                Auth::user()->role === 'admin'
            ),
            403,
            'คุณไม่สามารถส่งเอกสารนี้ได้'
        );

        $this->transition(
            document: $document,
            action: 'submit_user',
            toStatus: self::STATUS_RECEIVED,
            remark: $request->remark ?? 'ผู้ใช้ส่งเอกสาร',
            nextStep: self::STEP_CLERK
        );
    });

    return back()->with('success', 'ส่งเอกสารถึงธุรการเรียบร้อยแล้ว');
}

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ CLERK เสนอเอกสาร → ผอ.
    |--------------------------------------------------------------------------
    */
    public function submit(Request $request, Document $document)
{
    $this->authorizeRole(['clerk', 'admin']);

    $request->validate([
        'remark' => 'nullable|string|max:255',
    ]);

    DB::transaction(function () use ($document, $request) {
        $document->refresh();

        $this->ensureStatus($document, self::STATUS_RECEIVED);

        // ✅ ===== ADD START: generate doc_no per user =====
        // ✅ NEW: generate doc_no per department
if (is_null($document->doc_no)) {

    $departmentId = $document->department_id;

    $counter = DocumentCounter::firstOrCreate(
        ['department_id' => $departmentId],
        ['last_number' => 0]
    );

    $counter->increment('last_number');

    $document->update([
        'doc_no' => str_pad($counter->last_number, 3, '0', STR_PAD_LEFT),
    ]);
}
        // ✅ ===== ADD END =====

        $this->transition(
            document: $document,
            action: 'submit_clerk',
            toStatus: self::STATUS_WAITING_DIRECTOR,
            remark: $request->remark ?? 'เสนอผู้บริหาร',
            nextStep: self::STEP_DIRECTOR
        );
    });

    return back()->with('success', 'เสนอผู้บริหารเรียบร้อยแล้ว');
}

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ DIRECTOR อนุมัติ
    |--------------------------------------------------------------------------
    */
    public function approve(Document $document)
    {
        $this->authorizeRole(['director', 'admin']);

        DB::transaction(function () use ($document) {
            $document->refresh();

            $this->ensureStatus($document, self::STATUS_WAITING_DIRECTOR);

            $this->transition(
                document: $document,
                action: 'approve',
                toStatus: self::STATUS_APPROVED,
                remark: 'ผู้อำนวยการอนุมัติ',
                nextStep: self::STEP_CLERK
            );

            // notify owner
            $document->load('creator');
            if ($document->creator) {
                $document->creator->notify(
                    new DocumentApprovedNotification($document)
                );
            }

            // notify clerks
            User::where('role', 'clerk')->each(fn ($user) =>
                $user->notify(new DocumentApprovedNotification($document))
            );
        });

        return back()->with('success', 'อนุมัติเอกสารเรียบร้อยแล้ว');
    }

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ DIRECTOR ตีกลับ
    |--------------------------------------------------------------------------
    */
    public function reject(Request $request, Document $document)
    {
        $this->authorizeRole(['director', 'admin']);

        $request->validate([
            'remark' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($document, $request) {
            $document->refresh();

            $this->ensureStatus($document, self::STATUS_WAITING_DIRECTOR);

            $this->transition(
                document: $document,
                action: 'reject',
                toStatus: self::STATUS_REJECTED,
                remark: $request->remark,
                nextStep: self::STEP_CLERK
            );
        });

        return back()->with('success', 'ตีกลับเอกสารแล้ว');
    }

    /*
    |--------------------------------------------------------------------------
    | 5️⃣ CLERK แจกจ่าย
    |--------------------------------------------------------------------------
    */
    public function distribute(Request $request, Document $document)
    {
        $this->authorizeRole(['clerk', 'admin']);

        $data = $request->validate([
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
            'remark'     => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($document, $data) {
            $document->refresh();

            $this->ensureStatus($document, self::STATUS_APPROVED);

            abort_if(
                $document->distributions()->exists(),
                403,
                'เอกสารถูกแจกจ่ายไปแล้ว'
            );

            foreach ($data['user_ids'] as $userId) {
                DocumentDistribution::firstOrCreate([
                    'document_id' => $document->id,
                    'user_id'     => $userId,
                ]);
            }

            $this->transition(
                document: $document,
                action: 'distribute',
                toStatus: self::STATUS_DISTRIBUTED,
                remark: $data['remark'] ?? 'แจกจ่ายเอกสาร',
                nextStep: null
            );

            $document->load('distributions.user');

            $document->distributions->each(fn ($dist) =>
                $dist->user?->notify(
                    new DocumentDistributedNotification($document)
                )
            );
        });

        return back()->with('success', 'แจกจ่ายเอกสารเรียบร้อยแล้ว');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    private function authorizeRole(array $roles): void
    {
        abort_unless(
            in_array(Auth::user()->role, $roles),
            403,
            'คุณไม่มีสิทธิ์ทำรายการนี้'
        );
    }

    private function ensureStatus(Document $document, string $expected): void
    {
        abort_unless(
            $document->status === $expected,
            403,
            "ไม่สามารถทำรายการนี้ได้ในสถานะ {$document->status}"
        );
    }

    private function transition(
        Document $document,
        string $action,
        string $toStatus,
        string $remark,
        ?string $nextStep
    ): void {
        $fromStatus = $document->status;

        $document->update([
            'status'       => $toStatus,
            'current_step' => $nextStep,
        ]);

        DocumentLog::create([
            'document_id' => $document->id,
            'user_id'     => Auth::id(),
            'action'      => $action,
            'from_status' => $fromStatus,
            'to_status'   => $toStatus,
            'remark'      => $remark,
        ]);
    }
    public function cancel(Request $request, Document $document)
{
    if (! $document->canBeCancelled()) {
        abort(403, 'Document cannot be cancelled');
    }

    $request->validate([
        'remark' => 'required|string',
    ]);

    $document->update([
        'status' => 'cancelled',
    ]);

    DocumentTimeline::create([
        'document_id' => $document->id,
        'action' => 'cancel',
        'remark' => $request->remark,
        'actor_id' => auth()->id(),
    ]);

    return back();
}
}


