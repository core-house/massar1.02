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
            // Make holidays field nullable if it exists and is not already nullable
            if (Schema::hasColumn('projects', 'holidays')) {
                $table->string('holidays')->nullable()->change();
            }
            
            // Make weekly_holidays field nullable if it exists and is not already nullable
            if (Schema::hasColumn('projects', 'weekly_holidays')) {
                $table->string('weekly_holidays')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Revert holidays to not nullable (optional - usually we don't revert nullable changes)
            // if (Schema::hasColumn('projects', 'holidays')) {
            //     $table->string('holidays')->nullable(false)->change();
            // }
            
            // if (Schema::hasColumn('projects', 'weekly_holidays')) {
            //     $table->string('weekly_holidays')->nullable(false)->change();
            // }
        });
    }
};
