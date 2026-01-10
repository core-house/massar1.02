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
        Schema::table('daily_progress', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('employee_id')->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('employee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_progress', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            // Reverting nullable is tricky if data exists, but we'll attempt it
            // $table->unsignedBigInteger('employee_id')->nullable(false)->change(); 
        });
    }
};
