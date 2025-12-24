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
Schema::table('documents', function (Blueprint $table) {

    if (!Schema::hasColumn('documents', 'current_step')) {
        $table->string('current_step')->nullable();
    }

    if (!Schema::hasColumn('documents', 'submitted_at')) {
        $table->timestamp('submitted_at')->nullable();
    }

    if (!Schema::hasColumn('documents', 'approved_at')) {
        $table->timestamp('approved_at')->nullable();
    }

    if (!Schema::hasColumn('documents', 'rejected_at')) {
        $table->timestamp('rejected_at')->nullable();
    }

    if (!Schema::hasColumn('documents', 'distributed_at')) {
        $table->timestamp('distributed_at')->nullable();
    }
});
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            //
        });
    }
};
