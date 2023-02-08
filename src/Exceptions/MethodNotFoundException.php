<?php

namespace Awesome\Exceptions;

class MethodNotFoundException extends BaseException
{
    /**
     * Conditionally log the exception
     * @var bool
     */
    protected $shouldLog = false;

    /**
     * Default exception code
     * @var int
     */
    protected $defaultCode = 500;
}
