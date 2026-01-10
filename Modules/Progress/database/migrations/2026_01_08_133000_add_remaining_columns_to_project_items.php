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
        Schema::table('project_items', function (Blueprint $table) {
            if (!Schema::hasColumn('project_items', 'estimated_daily_qty')) {
                $table->decimal('estimated_daily_qty', 12, 2)->nullable()->after('daily_quantity');
            }
            if (!Schema::hasColumn('project_items', 'notes')) {
                // If subproject_name doesn't exist, we might have issue with 'after', 
                // but usually 'after' is ignored if column missing or just appends.
                // Assuming subproject_name exists from previous checks.
                $table->text('notes')->nullable()->after('subproject_name');
            }
            if (!Schema::hasColumn('project_items', 'is_measurable')) {
                $table->boolean('is_measurable')->default(true)->after('subproject_name'); // Placing after subproject_name or notes
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_items', function (Blueprint $table) {
            if (Schema::hasColumn('project_items', 'estimated_daily_qty')) {
                $table->dropColumn('estimated_daily_qty');
            }
            if (Schema::hasColumn('project_items', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('project_items', 'is_measurable')) {
                $table->dropColumn('is_measurable');
            }
        });
    }
};
