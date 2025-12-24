<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCounter extends Model
{
    protected $fillable = [
        'department_id',
        'last_number',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}

