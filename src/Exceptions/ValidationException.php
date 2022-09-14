<?php

namespace Awesome\Exceptions;

class ValidationException extends \Exception
{
    /**
     * Conditionally log the exception
     * @var bool
     */
    public $shouldLog = false;

    /**
     * ValidationException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = "", $code = 422, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
