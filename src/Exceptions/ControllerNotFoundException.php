<?php

namespace Awesome\Exceptions;

class ControllerNotFoundException extends \Exception
{
    /**
     * Conditionally log the exception
     * @var bool
     */
    public $shouldLog = false;

    /**
     * ControllerNotFoundException constructor.
     * @param string $controller
     * @param Exception|null $previous
     */
    public function __construct($controller, \Exception $previous = null)
    {
        parent::__construct("Controller class $controller not found", 500, $previous);
    }
}
