<?php

declare(strict_types=1);

namespace Modules\Agent\Exceptions;

use Exception;

/**
 * Exception thrown when a query execution exceeds the timeout limit.
 *
 * This occurs when:
 * - Query takes too long to execute
 * - max_execution_time is exceeded
 */
class QueryTimeoutException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message ?: __('agent.errors.query_timeout'), $code, $previous);
    }
}
