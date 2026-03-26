<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'draft'
        // Since Laravel's Schema builder for enum modification is tricky across DBs,
        // we use a raw statement for MySQL which is the environment here.
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 'cancelled', 'draft') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
