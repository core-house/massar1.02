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
            $table->decimal('unpaid_leave_days', 10, 2)->default(0)->after('absent_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_processings', function (Blueprint $table) {
            $table->dropColumn('unpaid_leave_days');
        });
    }
};
