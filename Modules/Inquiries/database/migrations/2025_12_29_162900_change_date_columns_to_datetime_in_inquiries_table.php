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
        Schema::table('inquiries', function (Blueprint $table) {

            $table->dateTime('estimation_start_date')->nullable()->change();
            $table->dateTime('estimation_finished_date')->nullable()->change();
            $table->dateTime('submitting_date')->nullable()->change();
            if (Schema::hasColumn('inquiries', 'assigned_engineer_date')) {
                $table->dateTime('assigned_engineer_date')->nullable()->change();
            } else {
                $table->dateTime('assigned_engineer_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->date('estimation_start_date')->nullable()->change();
            $table->date('estimation_finished_date')->nullable()->change();
            $table->date('submitting_date')->nullable()->change();

            if (Schema::hasColumn('inquiries', 'assigned_engineer_date')) {
                $table->date('assigned_engineer_date')->nullable()->change();
            }
        });
    }
};
