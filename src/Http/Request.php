<?php

namespace Awesome\Http;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Request class
 * @package Awesome
 */
class Request extends Message implements RequestInterface
{
    /**
     * Route parameters
     * @var array<mixed>
     */
    protected array $routeParams = [];

    /**
     * Uri
     * @var UriInterface
     */
    protected UriInterface $uri;

    /**
     * Request method
     * @var string
     */
    protected string $method;

    /**
     * Request constructor
     * @param string|null $method
     * @param array<mixed> $headers
     * @param Body|null $body
     * @param UriInterface|null $uri
     * @return void
     */
    public function __construct(
        string $method = null,
        array $headers = [],
        Body $body = null,
        UriInterface $uri = null
    ) {
        $this->method = $method ?? strtoupper($_SERVER['REQUEST_METHOD']);
        $this->headers = empty($headers) ? $headers : $this->extractHeaders();
        $stream = fopen('php://input', 'r');
        $this->body = $body ?? new Body($stream);
        $this->uri = $uri ?? new Uri(
            host: $_SERVER['HTTP_HOST'],
            port: $_SERVER['SERVER_PORT'],
            path: $_SERVER['REQUEST_URI'],
            query: $_SERVER['QUERY_STRING'],
            scheme: $_SERVER['REQUEST_SCHEME']
        );
    }

    /**
     * Get route params
     * @return array<mixed>
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }


    /**
     * Set route params
     * @param array<mixed> $routeParams
     * @return void
     */
    public function setRouteParams(array $routeParams): void
    {
        unset($routeParams['controller']);
        unset($routeParams['action']);

        $this->routeParams = $routeParams;

        foreach ($this->routeParams as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Get method
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set method
     * @param string $method
     * @return void
     */
    private function setMethod(string $method): void
    {
        $this->method = strtoupper($method);
    }

    /**
     * Extract headers
     * @return array<mixed>
     */
    public function extractHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get uri
     * @return UriInterface|Uri
     */
    public function getUri(): UriInterface|Uri
    {
        return $this->uri;
    }

    /**
     * Set uri
     * @param UriInterface $uri
     * @return void
     */
    public function setUri(UriInterface $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Validate if the request wants JSON
     * @return bool
     */
    public function wantsJson(): bool
    {
        return $this->getHeaderLine('Accept') === 'application/json';
    }

    /**
     * Retrieves the message's request target.
     * @return string
     */
    public function getRequestTarget(): string
    {
        return $this->uri->getPath() . '?' . $this->uri->getQuery();
    }

    /**
     * Return an instance with the specific request-target.
     * @param mixed $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget): static
    {
        $path = parse_url($requestTarget, PHP_URL_PATH);
        $query = parse_url($requestTarget, PHP_URL_QUERY);

        $uri = $this->uri
            ->withPath($path)
            ->withQuery($query);

        $this->setUri($uri);
        return $this;
    }

    /**
     * Return an instance with the provided HTTP method.
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method): static
    {
        $this->setMethod(strtoupper($method));
        return $this;
    }

    /**
     * Returns an instance with the provided URI.
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false): static
    {
        $this->setUri($uri);
        return $this;
    }
}
