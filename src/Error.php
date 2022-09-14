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
    private const ERROR_LOG_FOLDER = 'logs';

    /**
     * Error handler
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file Filename the error was raised in
     * @param int $line Line number in the file
     * @throws \ErrorException
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function exceptionHandler($exception)
    {
        $code = $exception->getCode() ?: 500;

        http_response_code($code);

        self::logError($exception);

        $request = container('Awesome\Request');
        $isDebugMode = $_ENV['APP_DEBUG'] === 'true';

        if ($request->wantsJson()) {
            self::exceptionToJson($exception, $isDebugMode);
            return;
        }

        if ($isDebugMode) {
            self::displayDebugError($exception);
            return;
        }

        self::renderException($exception, $code);
    }

    /**
     * Render exception
     * @param \Exception $exception
     * @param int $code
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private static function renderException(\Exception $exception, int $code)
    {
        $view = View::exists("$code.html") ? "$code.html" : '500.html';

        $errors = [];
        $message = $exception->getMessage();

        if (json_decode($exception->getMessage())) {
            $message = "Validation error";
            $errors = json_decode($exception->getMessage(), true);
        }

        echo View::make(
            $view,
            [
                'title' => $code,
                'code' => $code,
                'message' => $message,
                'errors' => $errors
            ]
        )->render();
    }

    /**
     * Parse exception to JSON
     * @param \Exception $exception
     * @param bool $isDebugMode
     * @return string
     */
    public static function exceptionToJson($exception, $isDebugMode)
    {
        $code = $exception->getCode() ?: 500;

        $response = [
            'message' => $exception->getMessage(),
        ];

        if (json_decode($exception->getMessage())) {
            $response['message'] = "Validation error";
            $response['errors'] = json_decode($exception->getMessage(), true);
        }

        if ($isDebugMode) {
            $response['trace'] = $exception->getTrace();
        }

        echo new Response($response, $code);
    }

    /**
     * Display error in debug mode
     * @param \Exception $exception
     */
    public function displayDebugError($exception)
    {
        $whoops = new Run();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler(new PrettyPageHandler());
        echo $whoops->handleException($exception);
    }

    public static function logError(\Exception $exception)
    {
        if (property_exists($exception, 'shouldLog') && $exception->shouldLog === false) {
            return;
        }
        
        $log = dirname($_SERVER['DOCUMENT_ROOT']) . '/' . self::ERROR_LOG_FOLDER . '/' . date('Y-m-d') . '.txt';
        ini_set('error_log', $log);
        $message = "Uncaught exception: '" . get_class($exception) . "'";
        $message .= " with message '" . $exception->getMessage() . "'";
        $message .= "\nStack trace: " . $exception->getTraceAsString();
        $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();
        error_log($message);
    }
}
