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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('contract_type_id')->constrained('contract_types');
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->decimal('fixed_work_hours', 10, 2)->nullable();
            $table->decimal('additional_work_hours', 10, 2)->nullable();
            $table->decimal('monthly_holidays', 10, 2)->nullable();
            $table->decimal('monthly_sick_days', 10, 2)->nullable();
            $table->text('information')->nullable();
            $table->foreignId('job_id')->nullable()->constrained('employees_jobs');
            $table->string('job_description')->nullable();
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index('contract_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
