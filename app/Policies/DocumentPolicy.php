<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Admin ทำได้ทุกอย่าง
     */
    public function before(User $user)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * ทุกคนที่ login ดูเอกสารได้
     */
    public function view(User $user, Document $document): bool
    {
        return true;
    }

    /**
     * ผู้สร้างแก้ไข / ลบได้ เฉพาะตอนยังเป็น draft
     */
    public function update(User $user, Document $document): bool
    {
        return $user->id === $document->created_by
            && $document->status === 'draft';
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->id === $document->created_by
            && $document->status === 'draft';
    }

    /**
     * ผู้สร้าง submit เอกสาร (draft → received)
     */
    public function submit(User $user, Document $document): bool
    {
        return $user->id === $document->created_by
            && $document->status === 'draft';
    }

    /**
     * Clerk รับเอกสาร
     */
    public function receive(User $user, Document $document): bool
    {
        return $user->role === 'clerk'
            && $document->status === 'submitted';
    }

    /**
     * Clerk ส่งให้ผอ.
     */
    public function sendToDirector(User $user, Document $document): bool
    {
        return $user->role === 'clerk'
            && $document->status === 'received';
    }

    /**
     * Director อนุมัติ
     */
    public function approve(User $user, Document $document): bool
    {
        return $user->role === 'director'
            && $document->status === 'waiting_director';
    }

    /**
     * Director ตีกลับ
     */
    public function reject(User $user, Document $document): bool
    {
        return $user->role === 'director'
            && $document->status === 'waiting_director';
    }

    /**
     * Clerk แจกจ่าย
     */
    public function distribute(User $user, Document $document): bool
    {
        return $user->role === 'clerk'
            && $document->status === 'approved';
    }
}
