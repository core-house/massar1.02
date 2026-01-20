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
        Schema::create('offline_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('local_transaction_id', 100)->unique()->comment('UUID من الجهاز المحلي');
            $table->unsignedBigInteger('server_transaction_id')->nullable()->comment('ID بعد المزامنة مع السيرفر');
            $table->unsignedBigInteger('user_id')->comment('المستخدم الذي أنشأ المعاملة');
            $table->unsignedBigInteger('branch_id')->nullable()->comment('الفرع التابع له المعاملة');
            
            $table->enum('status', ['pending', 'syncing', 'synced', 'error'])
                  ->default('pending')
                  ->comment('حالة المزامنة');
            
            $table->longText('transaction_data')->nullable()->comment('بيانات المعاملة بصيغة JSON');
            $table->text('error_message')->nullable()->comment('رسالة الخطأ في حالة فشل المزامنة');
            
            $table->integer('sync_attempts')->default(0)->comment('عدد محاولات المزامنة');
            $table->timestamp('last_sync_attempt')->nullable()->comment('آخر محاولة مزامنة');
            $table->timestamp('synced_at')->nullable()->comment('تاريخ نجاح المزامنة');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index('user_id');
            $table->index('branch_id');
            $table->index('server_transaction_id');
            $table->index(['branch_id', 'status']); // للفلترة per branch
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_sync_logs');
    }
};
