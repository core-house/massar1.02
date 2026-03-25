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
        Schema::create('sops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('category_id')->constrained('sop_categories')->onDelete('cascade');
            $table->unsignedBigInteger('department_id')->nullable(); // Reference to HR department if exists
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('attachment')->nullable();
            $table->string('version')->default('1.0');
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sops');
    }
};
