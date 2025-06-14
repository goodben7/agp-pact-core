<?php

namespace App\Exception;

class ComplainantCreationException extends \RuntimeException
{
    /**
     * Constructs a new ComplainantCreationException.
     *
     * @param string $message The exception message. Defaults to an empty string.
     * @param \Throwable|null $previous The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = '', ?\Throwable $previous = null)
    {
        // Call the parent RuntimeException constructor.
        // We set the code to 0 by default, as it's often not needed for custom exceptions,
        // but the message and previous exception are passed through.
        parent::__construct($message, 0, $previous);
    }
}