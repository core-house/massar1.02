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
            // Change holidays column from smallInteger to string to support comma-separated values (e.g. "5,6")
            $table->string('holidays')->default('5,6')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Revert back to smallInteger - WARNING: This might cause data truncation for string values
            // We default to 0 to simulate the old default
            $table->smallInteger('holidays')->default(0)->change();
        });
    }
};
