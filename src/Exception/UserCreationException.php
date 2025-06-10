<?php

namespace App\Exception;

class UserCreationException extends \RuntimeException
{
    public function __construct(string $message = '', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}