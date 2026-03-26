<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->foreignId('periodic_schedule_id')->nullable()
                ->after('service_type_id')
                ->constrained('periodic_maintenance_schedules')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['periodic_schedule_id']);
            $table->dropColumn('periodic_schedule_id');
        });
    }
};
