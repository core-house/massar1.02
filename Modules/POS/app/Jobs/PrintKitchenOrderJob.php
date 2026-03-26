<?php

declare(strict_types=1);

namespace Modules\POS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\POS\Models\PrintJob;

class PrintKitchenOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout;

    /**
     * Exponential backoff multiplier for retries.
     */
    public array $backoff;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public PrintJob $printJob
    ) {
        $this->tries = config('kitchen-printer.max_retries', 3);
        $this->timeout = config('kitchen-printer.timeout', 10);

        // Exponential backoff: 5s, 15s, 45s
        $this->backoff = [5, 15, 45];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if job can be auto-retried
        if (! $this->printJob->canAutoRetry() && $this->printJob->attempts > 0) {
            Log::info('Print job cannot be auto-retried', [
                'print_job_id' => $this->printJob->id,
                'error_type' => $this->printJob->error_type,
            ]);

            return;
        }

        // Mark as sending
        $this->printJob->markAsSending();

        try {
            // Send HTTP POST request to print agent
            $printAgentUrl = config('kitchen-printer.print_agent_url', 'http://localhost:5000/print');
            $timeout = config('kitchen-printer.timeout', 5);

            $response = Http::timeout($timeout)
                ->post($printAgentUrl, [
                    'printer' => $this->printJob->printerStation->printer_name,
                    'content' => $this->printJob->content,
                ]);

            $httpStatus = $response->status();
            $responseBody = $response->body();

            // Handle successful response
            if ($response->successful()) {
                $this->printJob->markAsPrinted($httpStatus, $responseBody);

                Log::info('Kitchen print successful', [
                    'print_job_id' => $this->printJob->id,
                    'transaction_id' => $this->printJob->transaction_id,
                    'printer_station_id' => $this->printJob->printer_station_id,
                    'attempts' => $this->printJob->attempts,
                ]);

                return;
            }

            // Classify error based on HTTP status
            $errorClassification = $this->classifyHttpError($httpStatus, $responseBody);

            $this->printJob->markAsFailed(
                $errorClassification['message'],
                $errorClassification['type'],
                $httpStatus,
                $responseBody,
                $errorClassification['can_auto_retry']
            );

            Log::warning('Kitchen print failed', [
                'print_job_id' => $this->printJob->id,
                'transaction_id' => $this->printJob->transaction_id,
                'printer_station_id' => $this->printJob->printer_station_id,
                'status' => $httpStatus,
                'error_type' => $errorClassification['type'],
                'can_auto_retry' => $errorClassification['can_auto_retry'],
            ]);

            // Retry only if error is temporary
            if ($errorClassification['can_auto_retry']) {
                throw new \Exception($errorClassification['message']);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Handle network errors (connection refused, timeout, etc.)
            $errorType = $this->isTimeout($e) ? 'TIMEOUT' : 'AGENT_DOWN';
            $errorMessage = $errorType === 'TIMEOUT'
                ? 'انتهت مهلة الاتصال بوكيل الطباعة'
                : 'فشل الاتصال بوكيل الطباعة';

            $this->printJob->markAsFailed(
                $errorMessage.': '.$e->getMessage(),
                $errorType,
                null,
                null,
                true // Can auto-retry
            );

            Log::error('Kitchen print connection exception', [
                'print_job_id' => $this->printJob->id,
                'transaction_id' => $this->printJob->transaction_id,
                'printer_station_id' => $this->printJob->printer_station_id,
                'error_type' => $errorType,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to trigger automatic retry
            throw $e;
        } catch (\Exception $e) {
            // Handle other exceptions
            $this->printJob->markAsFailed(
                'خطأ في معالجة الطباعة: '.$e->getMessage(),
                'UNKNOWN',
                null,
                null,
                true // Can auto-retry for unknown errors
            );

            Log::error('Kitchen print exception', [
                'print_job_id' => $this->printJob->id,
                'transaction_id' => $this->printJob->transaction_id,
                'printer_station_id' => $this->printJob->printer_station_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger automatic retry
            throw $e;
        }
    }

    /**
     * Classify HTTP error based on status code and response body.
     */
    private function classifyHttpError(int $httpStatus, string $responseBody): array
    {
        // 404: Printer not found
        if ($httpStatus === 404 || str_contains(strtolower($responseBody), 'printer not found')) {
            return [
                'type' => 'PRINTER_NOT_FOUND',
                'message' => 'الطابعة غير موجودة',
                'can_auto_retry' => false, // Logical error, don't auto-retry
            ];
        }

        // 400: Invalid payload
        if ($httpStatus === 400 || str_contains(strtolower($responseBody), 'invalid')) {
            return [
                'type' => 'INVALID_PAYLOAD',
                'message' => 'بيانات الطباعة غير صحيحة',
                'can_auto_retry' => false, // Logical error, don't auto-retry
            ];
        }

        // 503: Service unavailable (agent down)
        if ($httpStatus === 503) {
            return [
                'type' => 'AGENT_DOWN',
                'message' => 'وكيل الطباعة غير متاح',
                'can_auto_retry' => true, // Temporary error, can retry
            ];
        }

        // 5xx: Server errors (temporary)
        if ($httpStatus >= 500) {
            return [
                'type' => 'AGENT_DOWN',
                'message' => 'خطأ في خادم الطباعة',
                'can_auto_retry' => true, // Temporary error, can retry
            ];
        }

        // Other errors
        return [
            'type' => 'UNKNOWN',
            'message' => "خطأ HTTP {$httpStatus}",
            'can_auto_retry' => true, // Unknown errors can be retried
        ];
    }

    /**
     * Check if exception is a timeout.
     */
    private function isTimeout(\Exception $e): bool
    {
        return str_contains(strtolower($e->getMessage()), 'timeout') ||
               str_contains(strtolower($e->getMessage()), 'timed out');
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Kitchen print job failed permanently', [
            'print_job_id' => $this->printJob->id,
            'transaction_id' => $this->printJob->transaction_id,
            'printer_station_id' => $this->printJob->printer_station_id,
            'printer_name' => $this->printJob->printerStation->printer_name,
            'error_type' => $this->printJob->error_type,
            'attempts' => $this->printJob->attempts,
            'error' => $exception->getMessage(),
        ]);

        // Optionally, notify administrators about permanent failure
        // This could be implemented using Laravel notifications
    }
}
