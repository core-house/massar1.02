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
        Schema::create('agent_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('question_text', 1000);
            $table->text('answer_text')->nullable();
            $table->string('domain', 50)->nullable();
            $table->integer('result_count')->nullable();
            $table->integer('processing_time_ms')->nullable();
            $table->enum('status', ['pending', 'answered', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_questions');
    }
};
