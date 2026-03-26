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
        Schema::table('cvs', function (Blueprint $table) {
            $table->foreignId('job_posting_id')->nullable()->after('branch_id')->constrained('job_postings')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cvs', function (Blueprint $table) {
            $table->dropForeign(['job_posting_id']);
            $table->dropColumn('job_posting_id');
        });
    }
};
