<?php

namespace Awesome\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * Host
     * @var string
     */
    protected $host;

    /**
     * Port
     * @var int|null
     */
    protected $port;

    /**
     * Path
     * @var string
     */
    protected $path;

    /**
     * Query
     * @var string
     */
    protected $query;

    /**
     * Scheme
     * @var string
     */
    protected $scheme;

    /**
     * Uri constructor
     * @param string $host
     * @param int|null $port
     * @param string $path
     * @param string $query
     * @param string $scheme
     * @return void
     */
    public function __construct(
        string $host,
        $port,
        string $path,
        string $query,
        string $scheme
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->query = $query;
        $this->scheme = $scheme;
    }

    /**
     * Retrieve the scheme component of the URI.
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        return '';
    }

    /**
     * Retrieve the user information component of the URI.
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        return '';
    }

    /**
     * Retrieve the host component of the URI.
     * @return string The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     * @return null|int The URI port.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Retrieve the path component of the URI.
     * @return string The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return '';
    }

    /**
     * Return an instance with the specified scheme.
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * Return an instance with the specified user information.
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        return $this;
    }

    /**
     * Return an instance with the specified host.
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Return an instance with the specified port.
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Return an instance with the specified path.
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Return an instance with the specified query string.
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Return an instance with the specified URI fragment.
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        return $this;
    }

    /**
     * Return the string representation as a URI reference.
     * @return string
     */
    public function __toString()
    {
        $url = $this->scheme .
            '://' . $this->host .
            ':' . $this->port .
            $this->path .
            '?' . $this->query;

        // If not port, remove the colon
        if (!$this->port) {
            $url = str_replace($this->host . ':', $this->host, $url);
        }

        return $url;
    }
}
