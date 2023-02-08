<?php

namespace Awesome\Exceptions;

class DatabaseConnectionException extends BaseException
{
    /**
     * Conditionally log the exception
     * @var bool
     */
    public $shouldLog = true;

    /**
     * Default exception code
     * @var int
     */
    public $defaultCode = 500;
}
