<?php

declare(strict_types=1);

namespace Modules\POS\app\Services;

use Modules\POS\Models\CashierTransaction;
use Modules\POS\Models\PrintJob;

/**
 * Service for managing print job idempotency and outbox pattern.
 */
class PrintJobIdempotencyService
{
    /**
     * Create print job records inside transaction (Outbox pattern).
     *
     * This ensures print jobs are created atomically with the cashier transaction,
     * preventing lost prints even if the application crashes after transaction commit.
     */
    public function createPrintJobsInTransaction(
        CashierTransaction $transaction,
        array $printerStations,
        string $content
    ): array {
        $printJobs = [];
        $payloadHash = PrintJob::generatePayloadHash($content);

        foreach ($printerStations as $station) {
            // Check for existing print job with same idempotency key
            $sequence = $this->getNextSequence($transaction->id, $station->id);
            $idempotencyKey = PrintJob::generateIdempotencyKey(
                $transaction->id,
                $station->id,
                $payloadHash,
                $sequence
            );

            // Try to find existing job with this idempotency key
            $existingJob = PrintJob::where('idempotency_key', $idempotencyKey)->first();

            if ($existingJob) {
                // Job already exists, skip creation (idempotency protection)
                $printJobs[] = $existingJob;

                continue;
            }

            // Create new print job
            $printJob = PrintJob::create([
                'idempotency_key' => $idempotencyKey,
                'printer_station_id' => $station->id,
                'transaction_id' => $transaction->id,
                'content' => $content,
                'payload_hash' => $payloadHash,
                'sequence' => $sequence,
                'status' => 'queued',
                'error_type' => 'NONE',
                'attempts' => 0,
                'can_auto_retry' => true,
                'is_manual' => false,
            ]);

            $printJobs[] = $printJob;
        }

        return $printJobs;
    }

    /**
     * Get next sequence number for a transaction-station pair.
     */
    private function getNextSequence(int $transactionId, int $stationId): int
    {
        $maxSequence = PrintJob::where('transaction_id', $transactionId)
            ->where('printer_station_id', $stationId)
            ->max('sequence');

        return ($maxSequence ?? 0) + 1;
    }

    /**
     * Create print job for manual retry with audit logging.
     */
    public function createManualRetryJob(
        PrintJob $originalJob,
        int $userId
    ): PrintJob {
        // Get next sequence for this transaction-station pair
        $sequence = $this->getNextSequence(
            $originalJob->transaction_id,
            $originalJob->printer_station_id
        );

        // Generate new idempotency key with new sequence
        $idempotencyKey = PrintJob::generateIdempotencyKey(
            $originalJob->transaction_id,
            $originalJob->printer_station_id,
            $originalJob->payload_hash,
            $sequence
        );

        // Create new print job for retry
        $retryJob = PrintJob::create([
            'idempotency_key' => $idempotencyKey,
            'printer_station_id' => $originalJob->printer_station_id,
            'transaction_id' => $originalJob->transaction_id,
            'content' => $originalJob->content,
            'payload_hash' => $originalJob->payload_hash,
            'sequence' => $sequence,
            'status' => 'queued',
            'error_type' => 'NONE',
            'attempts' => 0,
            'can_auto_retry' => true,
            'is_manual' => true,
            'retried_by' => $userId,
            'retried_at' => now(),
        ]);

        return $retryJob;
    }

    /**
     * Check if a print job already exists for this transaction-station-content combination.
     */
    public function printJobExists(
        int $transactionId,
        int $stationId,
        string $content
    ): bool {
        $payloadHash = PrintJob::generatePayloadHash($content);
        $idempotencyKey = PrintJob::generateIdempotencyKey(
            $transactionId,
            $stationId,
            $payloadHash,
            1
        );

        return PrintJob::where('idempotency_key', $idempotencyKey)->exists();
    }
}
