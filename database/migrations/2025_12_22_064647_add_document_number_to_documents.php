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
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // database/migrations/xxxx_add_document_number_to_documents.php
Schema::table('documents', function (Blueprint $table) {
    $table->string('document_number', 3)->nullable()->after('user_id');
});
    }
};
