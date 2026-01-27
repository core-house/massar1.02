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
            // Add project_template_id
            $table->unsignedBigInteger('project_template_id')->nullable()->after('project_id');
            $table->foreign('project_template_id')->references('id')->on('project_templates')->cascadeOnDelete();

            // Make project_id nullable (need to drop FK first usually, but let's try modifying)
            // Note: modifying columns with foreign keys can be tricky.
            // Safest way:
            // 1. Drop FK
            // 2. Change column
            // 3. Re-add FK
        });

        Schema::table('project_items', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('project_items', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
        });

        Schema::table('project_items', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_items', function (Blueprint $table) {
            $table->dropForeign(['project_template_id']);
            $table->dropColumn('project_template_id');

            // We can't easily revert 'nullable' smoothly without ensuring no nulls exist,
            // but for down() strictly:
            // $table->unsignedBigInteger('project_id')->nullable(false)->change(); // Might fail if data exists
        });
    }
};
