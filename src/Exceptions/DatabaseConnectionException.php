<?php

namespace Awesome\Exceptions;

class DatabaseConnectionException extends \Exception
{
    /**
     * Conditionally log the exception
     * @var bool
     */
    public $shouldLog = true;

    /**
     * DatabaseConnectionException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 500, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
