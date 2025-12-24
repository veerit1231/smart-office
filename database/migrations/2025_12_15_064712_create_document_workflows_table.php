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
        Schema::create('document_workflows', function (Blueprint $table) {
    $table->id();

    $table->foreignId('document_id')->constrained()->cascadeOnDelete();

    $table->string('action'); // receive, assign, approve, reject
    $table->text('remark')->nullable();

    $table->foreignId('from_user_id')->nullable()->constrained('users');
    $table->foreignId('to_user_id')->nullable()->constrained('users');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_workflows');
    }
};
