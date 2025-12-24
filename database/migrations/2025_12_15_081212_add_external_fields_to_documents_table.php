<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {

            // ประเภทเอกสาร (internal / external)
            if (!Schema::hasColumn('documents', 'type')) {
                $table->string('type')->default('internal')->after('doc_no');
            }

            // หน่วยงานเจ้าของเรื่อง (รับนอก)
            if (!Schema::hasColumn('documents', 'department_id')) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();
            }

            // ไฟล์ PDF หลัก
            if (!Schema::hasColumn('documents', 'file_path')) {
                $table->string('file_path')->nullable();
            }

            // ข้อมูลเฉพาะรับนอก
            if (!Schema::hasColumn('documents', 'meta')) {
                $table->json('meta')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {

            if (Schema::hasColumn('documents', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }

            if (Schema::hasColumn('documents', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('documents', 'file_path')) {
                $table->dropColumn('file_path');
            }

            if (Schema::hasColumn('documents', 'meta')) {
                $table->dropColumn('meta');
            }
        });
    }
};
