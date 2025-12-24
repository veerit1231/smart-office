<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DocumentReportSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $departments = Department::pluck('id')->toArray();

        if (!$admin || count($departments) === 0) {
            $this->command->error('❌ ต้องมี admin และ department ก่อน');
            return;
        }

        $documents = [
            // ===============================
            // Incoming
            // ===============================
            [
                'doc_no' => 'D-001/2567',
                'type' => 'incoming',
                'title' => 'ขออนุมัติงบประมาณ',
                'status' => 'received',
                'department_id' => $departments[0],
                'created_at' => Carbon::now(),
            ],
            [
                'doc_no' => 'D-002/2567',
                'type' => 'incoming',
                'title' => 'แจ้งผลการตรวจสอบ',
                'status' => 'approved',
                'department_id' => $departments[0],
                'created_at' => Carbon::now(),
            ],
            [
                'doc_no' => 'D-003/2567',
                'type' => 'incoming',
                'title' => 'ขอข้อมูลเพิ่มเติม',
                'status' => 'waiting_director',
                'department_id' => $departments[1],
                'created_at' => Carbon::now()->subDay(),
            ],

            // ===============================
            // Internal
            // ===============================
            [
                'doc_no' => 'I-001/2567',
                'type' => 'internal',
                'title' => 'คำสั่งแต่งตั้งคณะทำงาน',
                'status' => 'approved',
                'department_id' => $departments[1],
                'created_at' => Carbon::now(),
            ],
            [
                'doc_no' => 'I-002/2567',
                'type' => 'internal',
                'title' => 'แจ้งเปลี่ยนแปลงระบบ',
                'status' => 'rejected',
                'department_id' => $departments[2] ?? $departments[0],
                'created_at' => Carbon::now()->subDays(7),
            ],
        ];

        foreach ($documents as $doc) {
            Document::create([
                'doc_no'        => $doc['doc_no'],
                'type'          => $doc['type'],
                'doc_year'      => now()->year + 543, // ปี พ.ศ. (ถ้าใช้)
                'title'         => $doc['title'],
                'department_id' => $doc['department_id'],
                'status'        => $doc['status'],
                'created_by'    => $admin->id,
                'created_at'    => $doc['created_at'],
                'updated_at'    => $doc['created_at'],
            ]);
        }

        $this->command->info('✅ Seed รายงานเอกสารเรียบร้อย (Smart Office)');
    }
}
