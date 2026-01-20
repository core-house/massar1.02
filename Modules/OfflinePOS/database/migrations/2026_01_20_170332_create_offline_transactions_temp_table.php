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
        Schema::create('offline_transactions_temp', function (Blueprint $table) {
            $table->id();
            $table->string('local_id', 100)->unique()->comment('UUID المحلي للمعاملة');
            $table->unsignedBigInteger('branch_id')->nullable()->comment('الفرع التابع له المعاملة');
            $table->json('data')->comment('بيانات المعاملة الكاملة');
            $table->enum('processing_status', ['queued', 'processing', 'completed', 'failed'])
                  ->default('queued')
                  ->comment('حالة المعالجة');
            $table->text('processing_error')->nullable()->comment('خطأ المعالجة');
            $table->timestamps();
            
            // Indexes
            $table->index('processing_status');
            $table->index('branch_id');
            $table->index('created_at');
            $table->index(['branch_id', 'processing_status']); // للفلترة per branch
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_transactions_temp');
    }
};
