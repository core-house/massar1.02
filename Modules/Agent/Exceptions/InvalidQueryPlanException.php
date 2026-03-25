<?php

declare(strict_types=1);

namespace Modules\Agent\Exceptions;

use Exception;

/**
 * Exception thrown when a query plan fails validation.
 *
 * This includes:
 * - Non-whitelisted columns in allowedColumns or filters
 * - LIKE search on non-searchable columns
 * - Forbidden columns in the plan
 * - Invalid limit values
 */
class InvalidQueryPlanException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string|array $errors = '', int $code = 0, ?\Throwable $previous = null)
    {
        $message = is_array($errors)
            ? __('agent.errors.invalid_query_plan', ['errors' => implode(', ', $errors)])
            : ($errors ?: __('agent.errors.invalid_query_plan', ['errors' => 'Unknown error']));

        parent::__construct($message, $code, $previous);
    }
}
