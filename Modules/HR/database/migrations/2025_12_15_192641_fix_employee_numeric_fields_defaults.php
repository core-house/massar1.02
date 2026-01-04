<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Ensure all numeric fields have default values
            // Set NULL values to 0 before changing the column
            DB::statement("UPDATE employees SET flexible_hourly_wage = 0 WHERE flexible_hourly_wage IS NULL");
            DB::statement("UPDATE employees SET allowed_permission_days = 0 WHERE allowed_permission_days IS NULL");
            DB::statement("UPDATE employees SET allowed_late_days = 0 WHERE allowed_late_days IS NULL");
            DB::statement("UPDATE employees SET allowed_absent_days = 0 WHERE allowed_absent_days IS NULL");
            DB::statement("UPDATE employees SET allowed_errand_days = 0 WHERE allowed_errand_days IS NULL");
            DB::statement("UPDATE employees SET is_errand_allowed = 0 WHERE is_errand_allowed IS NULL");

            // Change columns to ensure they have default values
            $table->decimal('flexible_hourly_wage', 10, 2)->default(0)->change();
            $table->unsignedInteger('allowed_permission_days')->default(0)->change();
            $table->unsignedInteger('allowed_late_days')->default(0)->change();
            $table->unsignedInteger('allowed_absent_days')->default(0)->change();
            $table->unsignedInteger('allowed_errand_days')->default(0)->change();
            $table->boolean('is_errand_allowed')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Revert to nullable (if needed)
            $table->decimal('flexible_hourly_wage', 10, 2)->nullable()->change();
            $table->unsignedInteger('allowed_permission_days')->nullable()->change();
            $table->unsignedInteger('allowed_late_days')->nullable()->change();
            $table->unsignedInteger('allowed_absent_days')->nullable()->change();
            $table->unsignedInteger('allowed_errand_days')->nullable()->change();
            $table->boolean('is_errand_allowed')->nullable()->change();
        });
    }
};
