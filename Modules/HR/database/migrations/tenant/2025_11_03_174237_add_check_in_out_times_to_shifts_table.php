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
        Schema::table('shifts', function (Blueprint $table) {
            $table->time('beginning_check_in')->nullable()->after('start_time');
            $table->time('ending_check_in')->nullable()->after('beginning_check_in');
            $table->time('beginning_check_out')->nullable()->after('end_time');
            $table->time('ending_check_out')->nullable()->after('beginning_check_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['beginning_check_in', 'ending_check_in', 'beginning_check_out', 'ending_check_out']);
        });
    }
};
