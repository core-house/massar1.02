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
            $table->unsignedBigInteger('subproject_id')->nullable()->after('subproject_name');
            $table->foreign('subproject_id')->references('id')->on('subprojects')->nullOnDelete();
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
