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
        $this->path = $path;
        $this->method = $method;
        $this->callback = $callback;
        $this->regexPath = $regexPath;
        $this->params = $params;
    }

    /**
     * Get route path
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get route method
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get route callback
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Get route regex path
     * @return string
     */
    public function getRegexPath()
    {
        return $this->regexPath;
    }

    /**
     * Set route regex path
     * @param string $regexPath
     * @return void
     */
    public function setRegexPath(string $regexPath)
    {
        $this->regexPath = $regexPath;
    }

    /**
     * Get route params
     * @return array<mixed>
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set route params
     * @param array<mixed> $params
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Has callable
     * @return bool
     */
    public function hasCallable()
    {
        return is_callable($this->callback);
    }

    /**
     * Call route callback
     * @param array<mixed> $args
     * @return mixed
     */
    public function call($args)
    {
        if (!$this->hasCallable()) {
            return null;
        }

        return call_user_func_array($this->callback, $args);
    }
}
