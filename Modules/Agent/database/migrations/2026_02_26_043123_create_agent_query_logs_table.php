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
        Schema::create('agent_query_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->nullable()->constrained('agent_questions')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('domain', 50);
            $table->string('table_name', 100);
            $table->enum('operation_type', ['select', 'count', 'aggregate']);
            $table->integer('column_count');
            $table->integer('filter_count');
            $table->integer('result_count');
            $table->integer('execution_time_ms');
            $table->json('scopes_applied');
            $table->timestamp('created_at');

            // Indexes for analytics
            $table->index('user_id');
            $table->index('domain');
            $table->index('table_name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_query_logs');
    }
};
