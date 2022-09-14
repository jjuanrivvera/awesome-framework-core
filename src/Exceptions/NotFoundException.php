<?php

namespace Awesome;

class NotFoundException extends \Exception
{
    public $shouldLog = false;

    /**
     * NotFoundException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = "", $code = 404, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
