<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add quantity column to manufacturing_orders
        if (!Schema::hasColumn('manufacturing_orders', 'quantity')) {
            Schema::table('manufacturing_orders', function (Blueprint $table) {
                $table->decimal('quantity', 10, 2)->default(0.00)->after('item_id');
            });
        }

        // 2. Migrate existing data: Sum stage quantities for each order
        $orderQuantities = DB::table('manufacturing_order_stage')
            ->select('manufacturing_order_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('manufacturing_order_id')
            ->get();

        foreach ($orderQuantities as $row) {
            DB::table('manufacturing_orders')
                ->where('id', $row->manufacturing_order_id)
                ->update(['quantity' => $row->total_qty]);
        }

        // 3. Drop quantity from manufacturing_order_stage
        if (Schema::hasColumn('manufacturing_order_stage', 'quantity')) {
            Schema::table('manufacturing_order_stage', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add quantity back to manufacturing_order_stage
        if (!Schema::hasColumn('manufacturing_order_stage', 'quantity')) {
            Schema::table('manufacturing_order_stage', function (Blueprint $table) {
                $table->integer('quantity')->default(0)->after('order');
            });
        }

        // 2. Migrate data back: Set stage quantity from order quantity
        $orders = DB::table('manufacturing_orders')->select('id', 'quantity')->get();
        foreach ($orders as $order) {
            DB::table('manufacturing_order_stage')
                ->where('manufacturing_order_id', $order->id)
                ->update(['quantity' => $order->quantity]);
        }

        // 3. Drop quantity from manufacturing_orders
        if (Schema::hasColumn('manufacturing_orders', 'quantity')) {
            Schema::table('manufacturing_orders', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }
};
