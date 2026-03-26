<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            // Idempotency key: derived from (transaction_id, station_id, payload_hash, sequence)
            $table->string('idempotency_key', 64)->after('id')->unique();

            // Payload hash for detecting content changes
            $table->string('payload_hash', 64)->after('content');

            // Sequence/version for same transaction reprints
            $table->unsignedInteger('sequence')->default(1)->after('payload_hash');

            // Enhanced state machine: queued -> sending -> printed or failed
            // Update existing status enum to include new states
            $table->enum('status', ['queued', 'sending', 'printed', 'failed'])
                ->default('queued')
                ->change();

            // Error classification
            $table->enum('error_type', [
                'AGENT_DOWN',
                'TIMEOUT',
                'PRINTER_NOT_FOUND',
                'INVALID_PAYLOAD',
                'UNKNOWN',
                'NONE',
            ])->default('NONE')->after('error_message');

            // Agent response details
            $table->unsignedSmallInteger('agent_http_status')->nullable()->after('error_type');
            $table->text('agent_response_body')->nullable()->after('agent_http_status');

            // Enhanced timestamps for state machine
            $table->timestamp('sent_at')->nullable()->after('printed_at');

            // Retry tracking
            $table->timestamp('last_retry_at')->nullable()->after('sent_at');
            $table->boolean('can_auto_retry')->default(true)->after('attempts');

            // Manual retry audit
            $table->foreignId('retried_by')
                ->nullable()
                ->after('printed_by')
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('retried_at')->nullable()->after('retried_by');

            // Add composite index for idempotency lookup
            $table->index(['transaction_id', 'printer_station_id', 'sequence'], 'idx_transaction_station_seq');

            // Add index for monitoring queries
            $table->index(['status', 'error_type', 'created_at'], 'idx_status_error_created');
            $table->index(['printer_station_id', 'status', 'created_at'], 'idx_station_status_created');
        });
    }

    public function down(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_transaction_station_seq');
            $table->dropIndex('idx_status_error_created');
            $table->dropIndex('idx_station_status_created');

            // Drop foreign key
            $table->dropForeign(['retried_by']);

            // Drop columns
            $table->dropColumn([
                'idempotency_key',
                'payload_hash',
                'sequence',
                'error_type',
                'agent_http_status',
                'agent_response_body',
                'sent_at',
                'last_retry_at',
                'can_auto_retry',
                'retried_by',
                'retried_at',
            ]);

            // Revert status enum to original values
            $table->enum('status', ['pending', 'success', 'failed', 'retrying'])
                ->default('pending')
                ->change();
        });
    }
};
