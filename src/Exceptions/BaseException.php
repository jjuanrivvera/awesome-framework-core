<?php

namespace Awesome\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    /**
     * Conditionally log the exception
     * @var bool
     */
    protected $shouldLog = true;

    /**
     * Default exception code
     * @var int
     */
    protected $defaultCode = 0;

    /**
     * BaseException constructor.
     * @param string $message
     * @param int|null $code
     * @param Exception|null $previous
     */
    public function __construct($message = "", $code = null, Exception $previous = null)
    {
        if (is_null($code)) {
            $code = $this->defaultCode;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Should the exception be logged?
     * @return bool
     */
    public function shouldLog(): bool
    {
        return $this->shouldLog;
    }
}
