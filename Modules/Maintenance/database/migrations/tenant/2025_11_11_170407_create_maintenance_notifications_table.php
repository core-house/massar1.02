<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periodic_schedule_id')
                ->constrained('periodic_maintenance_schedules')
                ->cascadeOnDelete();
            $table->date('notification_date');
            $table->enum('status', ['pending', 'sent', 'read'])->default('pending');
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_notifications');
    }
};
