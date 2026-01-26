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
        Schema::table('cashier_transactions', function (Blueprint $table) {
            $table->enum('status', ['draft', 'held', 'completed'])->default('draft')->after('sync_status')->comment('حالة الفاتورة: draft=مسودة, held=معلقة, completed=مكتملة');
            $table->timestamp('held_at')->nullable()->after('status')->comment('تاريخ التعليق');
            $table->timestamp('completed_at')->nullable()->after('held_at')->comment('تاريخ الإكمال');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashier_transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'held_at', 'completed_at']);
        });
    }
};
