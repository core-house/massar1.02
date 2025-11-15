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
        Schema::table('attendance_processings', function (Blueprint $table) {
            $table->decimal('total_late_hours', 10, 2)->default(0)->after('overtime_work_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_processings', function (Blueprint $table) {
            $table->dropColumn('total_late_hours');
        });
    }
};
