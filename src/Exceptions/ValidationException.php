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
     * @param array $errors
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($errors = [], $code = 422, \Exception $previous = null)
    {
        parent::__construct(json_encode($errors), $code, $previous);
    }
}
