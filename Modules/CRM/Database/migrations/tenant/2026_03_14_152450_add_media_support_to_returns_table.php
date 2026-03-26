<?php

declare(strict_types=1);

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
        // Note: We keep the 'attachment' column for backward compatibility
        // The new system uses Spatie Media Library for better file management
        // Old attachments can be migrated manually if needed
        
        // No schema changes needed - Media Library uses its own 'media' table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes to reverse
    }
};
