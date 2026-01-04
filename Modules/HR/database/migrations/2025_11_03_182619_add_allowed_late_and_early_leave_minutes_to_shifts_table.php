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
            $table->integer('allowed_late_minutes')->nullable()->default(0)->after('ending_check_in')->comment('الوقت المسموح به للتأخير بعد بداية وقت الدخول بالدقائق');
            $table->integer('allowed_early_leave_minutes')->nullable()->default(0)->after('ending_check_out')->comment('الوقت المسموح به للخروج المبكر قبل نهاية الوردية بالدقائق');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['allowed_late_minutes', 'allowed_early_leave_minutes']);
        });
    }
};
