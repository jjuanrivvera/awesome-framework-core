<?php

namespace Awesome;

class ValidationException extends \Exception
{
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
