<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodic_maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('item_name'); // اسم البند (مثلاً: فلتر)
            $table->string('item_number'); // رقم البند
            $table->string('client_name');
            $table->string('client_phone');
            $table->foreignId('service_type_id')->constrained('service_types')->cascadeOnDelete();

            // معلومات الجدولة
            $table->enum('frequency_type', ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual', 'custom_days']);
            $table->integer('frequency_value')->nullable(); // للأيام المخصصة (مثلاً: 180 يوم)
            $table->date('start_date');
            $table->date('next_maintenance_date');
            $table->date('last_maintenance_date')->nullable();

            // التنبيهات
            $table->integer('notification_days_before')->default(7); // إرسال تنبيه قبل كم يوم
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->foreignId('branch_id')->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodic_maintenance_schedules');
    }
};
