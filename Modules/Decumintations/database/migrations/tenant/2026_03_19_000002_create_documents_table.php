<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 50)->nullable();
            $table->unsignedBigInteger('file_size')->nullable()->comment('bytes');
            $table->json('tags')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('related'); // related_type, related_id
            $table->softDeletes();
            $table->timestamps();

            $table->index('expiry_date');
            $table->index('is_confidential');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
