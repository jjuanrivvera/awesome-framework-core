<?php

namespace Awesome;

/**
 * Request class
 * @package Awesome
 */
class Request
{
    /**
     * Route parameters
     * @var array<mixed>
     */
    protected $routeParams = [];

    /**
     * Path
     * @var string
     */
    protected $path;

    /**
     * Uri
     * @var string
     */
    protected $uri;

    /**
     * Request method
     * @var string
     */
    protected $method;

    /**
     * Request headers
     * @var array<mixed>
     */
    protected $headers;

    /**
     * Request body
     * @var string
     */
    protected $body;

    /**
     * Request constructor
     * @return void
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = $this->extractHeaders();
        $this->body = file_get_contents('php://input');
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get route params
     * @return array<mixed>
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }


    /**
     * Set route params
     * @param array<mixed> $routeParams
     * @return void
     */
    public function setRouteParams(array $routeParams)
    {
        unset($routeParams['controller']);
        unset($routeParams['action']);

        $this->routeParams = $routeParams;

        foreach ($this->routeParams as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Get path
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get method
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set method
     * @param string $method
     * @return void
     */
    private function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * Get headers
     * @return array<mixed>
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set headers
     * @param array<mixed> $headers
     * @return void
     */
    private function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Extract headers
     * @return array<mixed>
     */
    public function extractHeaders()
    {
        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(
                    ' ',
                    '-',
                    ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))
                )] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get body
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get uri
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set uri
     * @param string $uri
     * @return void
     */
    public function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    /**
     * Validate if the request wants JSON
     * @return bool
     */
    public function wantsJson()
    {
        return $this->headers['Accept'] === 'application/json';
    }
}
