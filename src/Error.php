<?php

namespace Awesome;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

/**
 * Error and exception handler
 * @package Awesome
 */
class Error
{
    /**
     * Error handler
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file Filename the error was raised in
     * @param int $line Line number in the file
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }
    
    /**
     * Exception handler
     * @param \Exception $exception The exception
     */
    public static function exceptionHandler($exception)
    {
        $code = $exception->getCode();

        if ($_ENV['APP_DEBUG'] === 'true') {
            $whoops = new Run();
            $whoops->allowQuit(false);
            $whoops->writeToOutput(false);
            $whoops->pushHandler(new PrettyPageHandler());
            echo $whoops->handleException($exception);

            return;
        }

        if ($code != 404) {
            $code = 500;
        }

        http_response_code($code);

        self::logError($exception);

        echo View::make(
            "$code.html",
            [
                'title' => $code,
                'code' => $code,
                'message' => $exception->getMessage()
            ]
        )->render();
    }

    public static function logError(\Exception $exception)
    {
        $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
        ini_set('error_log', $log);
        $message = "Uncaught exception: '" . get_class($exception) . "'";
        $message .= " with message '" . $exception->getMessage() . "'";
        $message .= "\nStack trace: " . $exception->getTraceAsString();
        $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();
        error_log($message);
    }
}
