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
        Schema::table('operhead', function (Blueprint $table) {
            $table->enum('order_type', ['dining', 'takeaway', 'delivery'])->nullable()->after('pro_type');
            $table->unsignedBigInteger('table_id')->nullable()->after('order_type');
            $table->unsignedBigInteger('driver_id')->nullable()->after('table_id');
            $table->unsignedBigInteger('contact_id')->nullable()->after('driver_id');
            $table->unsignedBigInteger('price_group_id')->nullable()->after('contact_id');
            $table->decimal('delivery_fee', 15, 2)->default(0)->after('price_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operhead', function (Blueprint $table) {
            $table->dropColumn([
                'order_type',
                'table_id',
                'driver_id',
                'contact_id',
                'price_group_id',
                'delivery_fee'
            ]);
        });
    }
};
