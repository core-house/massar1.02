<?php

declare(strict_types=1);

namespace Modules\Agent\Exceptions;

use Exception;

/**
 * Exception thrown when a question cannot be classified or is invalid.
 *
 * This includes:
 * - Multi-intent questions (containing multiple domains)
 * - Unmappable questions (no recognized domain keywords)
 * - Malformed questions
 */
class InvalidQuestionException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message ?: __('agent.errors.unmappable_question'), $code, $previous);
    }
}
