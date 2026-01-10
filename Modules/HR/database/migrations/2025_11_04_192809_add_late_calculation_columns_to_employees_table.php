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
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('late_hour_calculation', 10, 2)->nullable()->after('additional_day_calculation');
            $table->decimal('late_day_calculation', 10, 2)->nullable()->after('late_hour_calculation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['late_hour_calculation', 'late_day_calculation']);
        });
    }
};
