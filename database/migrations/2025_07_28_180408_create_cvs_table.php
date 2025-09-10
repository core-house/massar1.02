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
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->required();
            $table->string('email')->nullable();
            $table->string('phone')->required();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('birth_date')->required();
            $table->string('gender')->required();
            $table->string('marital_status')->required();
            $table->string('nationality')->required();
            $table->string('religion')->required();
            $table->text('summary')->nullable();
            $table->text('skills')->nullable();
            $table->text('experience')->nullable();
            $table->text('education')->nullable();
            $table->text('projects')->nullable();
            $table->text('certifications')->nullable();
            $table->text('languages')->nullable();
            $table->text('interests')->nullable();
            $table->text('references')->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('portfolio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};
