<?php

namespace Awesome;

use Whoops\Run;
use Awesome\Http\Response;
use Whoops\Handler\PrettyPageHandler;

/**
 * Error and exception handler
 * @package Awesome
 */
class Error
{
    /**
     * Error log folder
     * @var string
     */
    private const ERROR_LOG_FOLDER = 'logs';

    /**
     * Error handler
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file Filename the error was raised in
     * @param int $line Line number in the file
     * @return void
     *@throws \ErrorException
     */
    public static function errorHandler(int $level, string $message, string $file, int $line): void
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler
     * @param \Throwable $exception The exception
     * @return void
     * @throws \DI\NotFoundException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \DI\DependencyException
     */
    public static function exceptionHandler(\Throwable $exception): void
    {
        $code = $exception->getCode() ?: 500;

        http_response_code($code);

        self::logError($exception);

        $request = container('Awesome\Http\Request');
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
     * @param \Throwable $exception
     * @param int $code
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @return void
     */
    private static function renderException(\Throwable $exception, int $code): void
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
     * @param \Throwable $exception
     * @param bool $isDebugMode
     * @return void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public static function exceptionToJson(\Throwable $exception, bool $isDebugMode): void
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
     * @param \Throwable $exception
     * @return void
     */
    public static function displayDebugError(\Throwable $exception): void
    {
        $whoops = new Run();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler(new PrettyPageHandler());
        echo $whoops->handleException($exception);
    }

    /**
     * Log the error
     * @param mixed $exception
     * @return void
     */
    public static function logError($exception): void
    {
        if (method_exists($exception, 'shouldLog') && !$exception->shouldLog()) {
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
