<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->enum('type', ['person', 'organization']);

            // Contact Details
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('website')->nullable();

            // Address
            $table->text('address')->nullable();
            $table->text('address2')->nullable();

            $table->string('tax_number')->nullable();
            $table->boolean('is_active')->default(true);

            // Additional Info
            $table->text('notes')->nullable();

            // System fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
