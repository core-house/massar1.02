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
        Schema::create('contact_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Client, Consultant, Main Contractor, Owner, Engineer
            $table->string('slug')->unique(); // client, consultant, main_contractor, owner, engineer
            $table->string('description')->nullable();
            $table->string('icon')->nullable(); // FontAwesome icon
            $table->string('color')->nullable(); // Tailwind color class
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_roles');
    }
};
