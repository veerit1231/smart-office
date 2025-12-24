<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\DocumentDistributedNotification;

class DocumentDistribution extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'is_read',
        'read_at',
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}
}
