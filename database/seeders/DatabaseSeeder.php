<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Document;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $dept = Department::create([
            'name' => 'ฝ่ายธุรการ',
            'code' => 'ADMIN',
        ]);

        Document::create([
            'doc_no' => '001',
            'doc_year' => 2567,
            'title' => 'หนังสือขออนุมัติ',
            'department_id' => $dept->id,
            'created_by' => $user->id,
            'status' => 'received',
        ]);
    }
}
