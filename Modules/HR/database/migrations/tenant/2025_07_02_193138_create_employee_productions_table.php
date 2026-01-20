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
        Schema::create('employee_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('attendance_processing_id')->constrained('attendance_processings')->onDelete('cascade');
            $table->unsignedBigInteger('units_count')->required();
            $table->unsignedBigInteger('units_price')->required();
            $table->unsignedBigInteger('units_total_price')->required();
            $table->timestamps();
            // index
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_productions');
    }
};
