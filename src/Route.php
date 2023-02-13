<?php

namespace Awesome;

/**
 * Route class
 * @package Awesome
 */
class Route
{
    /**
     * Route constructor
     * @throws \Throwable
     * @return void
     */
    public function __construct(
        protected string $path,
        protected string $method,
        protected mixed $callback,
        protected string $regexPath = '',
        protected array $params = []
    ) {
        //
    }

    /**
     * Get route path
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get route method
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get route callback
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Get route regex path
     * @return string
     */
    public function getRegexPath(): string
    {
        return $this->regexPath;
    }

    /**
     * Set route regex path
     * @param string $regexPath
     * @return void
     */
    public function setRegexPath(string $regexPath): void
    {
        $this->regexPath = $regexPath;
    }

    /**
     * Get route params
     * @return array<mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set route params
     * @param array<mixed> $params
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Has callable
     * @return bool
     */
    public function hasCallable(): bool
    {
        return is_callable($this->callback);
    }

    /**
     * Call route callback
     * @param array<mixed> $args
     * @return mixed
     */
    public function call(array $args): mixed
    {
        if (!$this->hasCallable()) {
            return null;
        }

        return call_user_func_array($this->callback, $args);
    }
}
