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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('weekly_holidays')->nullable()->after('daily_work_hours');
        });

        Schema::table('project_items', function (Blueprint $table) {
            $table->string('subproject_name')->nullable()->after('work_item_id');
            $table->integer('duration')->nullable()->after('end_date');
            $table->string('predecessor')->nullable()->after('duration');
            $table->enum('dependency_type', ['end_to_start', 'start_to_start'])->default('end_to_start')->after('predecessor');
            $table->integer('lag')->default(0)->after('dependency_type');
            $table->integer('item_order')->default(0)->after('lag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('weekly_holidays');
        });

        Schema::table('project_items', function (Blueprint $table) {
            $table->dropColumn(['subproject_name', 'duration', 'predecessor', 'dependency_type', 'lag', 'item_order']);
        });
    }
};
