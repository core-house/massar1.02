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
        // 1. Add 'stopped' to manufacturing_orders ENUM
        DB::statement("ALTER TABLE manufacturing_orders MODIFY COLUMN status ENUM('draft', 'in_progress', 'completed', 'cancelled', 'stopped') DEFAULT 'draft'");

        // 2. Update data in manufacturing_orders
        DB::table('manufacturing_orders')
            ->whereIn('status', ['draft', 'cancelled'])
            ->update(['status' => 'stopped']);

        // 3. Finalize manufacturing_orders ENUM
        DB::statement("ALTER TABLE manufacturing_orders MODIFY COLUMN status ENUM('stopped', 'in_progress', 'completed') DEFAULT 'stopped'");

        // 4. Add 'stopped' to manufacturing_order_stage ENUM
        DB::statement("ALTER TABLE manufacturing_order_stage MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 'on_hold', 'cancelled', 'stopped') DEFAULT 'pending'");

        // 5. Update data in manufacturing_order_stage
        DB::table('manufacturing_order_stage')
            ->whereIn('status', ['pending', 'on_hold', 'cancelled'])
            ->update(['status' => 'stopped']);

        // 6. Finalize manufacturing_order_stage ENUM
        DB::statement("ALTER TABLE manufacturing_order_stage MODIFY COLUMN status ENUM('stopped', 'in_progress', 'completed') DEFAULT 'stopped'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert manufacturing_orders status column
        DB::statement("ALTER TABLE manufacturing_orders MODIFY COLUMN status ENUM('draft', 'in_progress', 'completed', 'cancelled') DEFAULT 'draft'");

        // 2. Revert manufacturing_order_stage status column
        DB::statement("ALTER TABLE manufacturing_order_stage MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 'on_hold', 'cancelled') DEFAULT 'pending'");
        
        // Data mapping back is not strictly necessary as we've already modified the enum options
    }
};
