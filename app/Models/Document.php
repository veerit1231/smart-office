<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Department;
use App\Models\DocumentWorkflow;
use App\Models\DocumentAttachment;
use App\Models\DocumentLog;
use App\Models\DocumentDistribution;
use App\Models\User;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    /**
     * Status labels (ภาษาไทย) – ใช้เป็นศูนย์กลางทั้งระบบ
     */
    public const STATUS_LABELS = [
        'draft' => 'ร่าง',
        'received' => 'รับเรื่องแล้ว',
        'waiting_director' => 'รอผู้อำนวยการ',
        'approved' => 'อนุมัติ',
        'rejected' => 'ไม่อนุมัติ',
        'distributed' => 'แจกจ่ายแล้ว',
        'cancelled' => 'ยกเลิก',
    ];

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'doc_no',
        'doc_year',
        'title',
        'type',           // internal | incoming
        'status',         // draft | received | waiting_director | approved | rejected | distributed | cancelled
        'current_step',   // clerk | director | null
        'created_by',
        'department_id',
        'file_path',
        'meta',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'meta' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // หน่วยงานเจ้าของเรื่อง
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Workflow history
    public function workflows()
    {
        return $this->hasMany(DocumentWorkflow::class);
    }

    // ไฟล์แนบ
    public function attachments()
    {
        return $this->hasMany(DocumentAttachment::class);
    }

    // Timeline / logs
    public function logs()
    {
        return $this->hasMany(DocumentLog::class)->latest();
    }

    // ผู้สร้างเอกสาร
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // การแจกจ่ายเอกสาร
    public function distributions()
    {
        return $this->hasMany(DocumentDistribution::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Inbox สำหรับ clerk / director
     */
    public function scopeInbox($query)
    {
        return $query->whereIn('status', [
            'received',           // user → clerk
            'waiting_director',   // clerk → director
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / Business Logic
    |--------------------------------------------------------------------------
    */

    // เป็นเอกสารรับนอก
    public function isIncoming(): bool
    {
        return $this->type === 'incoming';
    }

    // เป็น draft
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    // ถึงขั้น clerk
    public function isAtClerk(): bool
    {
        return $this->current_step === 'clerk';
    }

    // ถึงขั้น director
    public function isAtDirector(): bool
    {
        return $this->current_step === 'director';
    }

    // สามารถยกเลิกเอกสารได้หรือไม่
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [
            'approved',
            'distributed',
            'cancelled',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * label สถานะภาษาไทย (ใช้แสดงผล)
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
