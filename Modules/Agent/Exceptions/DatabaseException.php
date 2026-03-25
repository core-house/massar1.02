<?php

declare(strict_types=1);

namespace Modules\Agent\Exceptions;

use Exception;

/**
 * Exception thrown when a database operation fails.
 *
 * This includes:
 * - Transaction failures
 * - Connection errors
 * - Query execution errors
 */
class DatabaseException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message ?: __('agent.errors.database_error'), $code, $previous);
    }
}
