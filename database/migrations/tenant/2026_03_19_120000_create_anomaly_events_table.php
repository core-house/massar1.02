<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomaly_events', function (Blueprint $table) {
            $table->id();

            $table->morphs('subject'); // subject_type, subject_id

            $table->string('rule_code', 120);
            $table->string('severity', 20)->default('warning'); // info|warning|critical
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->json('meta')->nullable();

            // Used to prevent duplicates for same state
            $table->string('fingerprint', 64)->unique();

            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();

            $table->timestamps();

            $table->index(['subject_type', 'subject_id', 'rule_code']);
            $table->index(['severity', 'resolved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomaly_events');
    }
};

