<?php

declare(strict_types=1);

namespace Modules\Agent\Exceptions;

use Exception;

/**
 * Exception thrown when a domain is not supported by the system.
 *
 * This occurs when:
 * - A domain service is requested but not implemented
 * - A domain is recognized but not configured
 */
class UnsupportedDomainException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message ?: __('agent.errors.unsupported_domain'), $code, $previous);
    }
}
