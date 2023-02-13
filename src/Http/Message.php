<?php

namespace Awesome\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

/**
 * Message class
 * @package Awesome
 */
abstract class Message implements MessageInterface
{
    /**
     * Protocol version
     * @var string
     */
    protected string $protocolVersion = '1.1';

    /**
     * Message headers
     * @var array<mixed>
     */
    protected array $headers = [];

    /**
     * Message body
     * @var StreamInterface
     */
    protected StreamInterface $body;

    /**
     * Retrieves the HTTP protocol version as a string.
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     * @param string $version HTTP protocol version
     * @return static
     */
    public function withProtocolVersion($version): static
    {
        $this->protocolVersion = $version;
        return $this;
    }

    /**
     * Get headers
     * @return array<mixed>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name): bool
    {
        return isset($this->headers[$name]);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name): array
    {
        if (!isset($this->headers[$name])) {
            return [];
        }

        return $this->headers[$name];
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name): string
    {
        if (!isset($this->headers[$name])) {
            return '';
        }

        return implode(',', $this->headers[$name]);
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value): static
    {
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value): static
    {
        if (is_array($value)) {
            $this->headers[$name] = array_merge($this->headers[$name], $value);
        } else {
            $this->headers[$name][] = $value;
        }

        return $this;
    }

    /**
     * Return an instance without the specified header.
     * @param string $name Case-insensitive header field name to remove.
     * @return static
     */
    public function withoutHeader($name): static
    {
        unset($this->headers[$name]);
        return $this;
    }

    /**
     * Gets the body of the message.
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * Return an instance with the specified message body.
     * @param StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body): static
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get the message body as a string
     * @return string
     */
    public function __toString()
    {
        return $this->body;
    }

    /**
     * Get the message body as a string
     * @return string
     */
    public function toString(): string
    {
        return (string) $this->body;
    }
}
