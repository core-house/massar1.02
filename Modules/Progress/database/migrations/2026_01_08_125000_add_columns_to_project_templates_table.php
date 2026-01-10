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
        Schema::table('project_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('project_type_id')->nullable()->after('description');
            $table->json('weekly_holidays')->nullable()->after('project_type_id');
            
            $table->foreign('project_type_id')->references('id')->on('project_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_templates', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn(['project_type_id', 'weekly_holidays']);
        });
    }
};
