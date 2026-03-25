<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // حسابات واجهة المطعم
            $table->unsignedBigInteger('restaurant_kitchen_store')->nullable()->comment('مخزن المطبخ');
            $table->unsignedBigInteger('restaurant_operating_account')->nullable()->comment('مركز التشغيل (حساب وسيط)');
            $table->unsignedBigInteger('restaurant_sales_account')->nullable()->comment('حساب المبيعات للمطعم');
            $table->unsignedBigInteger('restaurant_cogs_account')->nullable()->comment('حساب تكلفة البضاعة المباعة');
            $table->unsignedBigInteger('restaurant_inventory_account')->nullable()->comment('حساب المخزون');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'restaurant_kitchen_store',
                'restaurant_operating_account',
                'restaurant_sales_account',
                'restaurant_cogs_account',
                'restaurant_inventory_account',
            ]);
        });
    }
};
