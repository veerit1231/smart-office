<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('document_counters', function (Blueprint $table) {
        // ลบ foreign key ก่อน
        $table->dropForeign(['department_id']);

        // ลบ unique index
        $table->dropUnique(['department_id']);

        // ลบ column
        $table->dropColumn('department_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('document_counters', function (Blueprint $table) {
        $table->foreignId('department_id')->constrained()->cascadeOnDelete();
        $table->unique('department_id');
    });
}
};
