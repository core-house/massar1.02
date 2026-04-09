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
        // First, modify the status column to include both old and new enum values
        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('مفعل', 'معطل', 'مقيم', 'مواطن', 'زائر', 'خارج الشركة') DEFAULT 'مواطن'");

        // Update existing status values to new values
        DB::statement("UPDATE employees SET status = 'مواطن' WHERE status = 'مفعل'");
        DB::statement("UPDATE employees SET status = 'خارج الشركة' WHERE status = 'معطل'");

        // Finally, modify the status column to use only new enum values
        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('مقيم', 'مواطن', 'زائر', 'خارج الشركة') DEFAULT 'مواطن'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, modify the status column to include both old and new enum values
        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('مفعل', 'معطل', 'مقيم', 'مواطن', 'زائر', 'خارج الشركة') DEFAULT 'مفعل'");

        // Revert status values back to old values
        DB::statement("UPDATE employees SET status = 'مفعل' WHERE status = 'مواطن'");
        DB::statement("UPDATE employees SET status = 'معطل' WHERE status = 'خارج الشركة'");

        // Finally, revert the status column to old enum values
        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('مفعل', 'معطل') DEFAULT 'مفعل'");
    }
};
