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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('enable_scale_items')->default(false)->after('def_pos_fund');
            $table->string('scale_code_prefix', 10)->nullable()->after('enable_scale_items');
            $table->integer('scale_code_digits')->default(5)->after('scale_code_prefix');
            $table->integer('scale_quantity_digits')->default(5)->after('scale_code_digits');
            $table->integer('scale_quantity_divisor')->default(100)->after('scale_quantity_digits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'enable_scale_items',
                'scale_code_prefix',
                'scale_code_digits',
                'scale_quantity_digits',
                'scale_quantity_divisor'
            ]);
        });
    }
};
