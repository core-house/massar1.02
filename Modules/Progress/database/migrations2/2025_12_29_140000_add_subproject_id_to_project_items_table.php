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
            // Add subproject_id if it doesn't exist
            if (!Schema::hasColumn('project_items', 'subproject_id')) {
                $table->foreignId('subproject_id')
                      ->nullable()
                      ->after('project_id')
                      ->constrained('subprojects')
                      ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_items', function (Blueprint $table) {
            $table->dropForeign(['subproject_id']);
            $table->dropColumn('subproject_id');
        });
    }
};
