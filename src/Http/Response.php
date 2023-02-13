<?php

namespace Awesome\Http;

use Psr\Http\Message\ResponseInterface;

final class Response extends Message implements ResponseInterface
{
    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;
    public const HTTP_EARLY_HINTS = 103;
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207;
    public const HTTP_ALREADY_REPORTED = 208;
    public const HTTP_IM_USED = 226;
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENTLY_REDIRECT = 308;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_I_AM_A_TEAPOT = 418;
    public const HTTP_MISDIRECTED_REQUEST = 421;
    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    public const HTTP_LOCKED = 423;
    public const HTTP_FAILED_DEPENDENCY = 424;
    public const HTTP_TOO_EARLY = 425;
    public const HTTP_UPGRADE_REQUIRED = 426;
    public const HTTP_PRECONDITION_REQUIRED = 428;
    public const HTTP_TOO_MANY_REQUESTS = 429;
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;
    public const HTTP_INSUFFICIENT_STORAGE = 507;
    public const HTTP_LOOP_DETECTED = 508;
    public const HTTP_NOT_EXTENDED = 510;
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $mimeType = 'text/html';

    /**
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * @var string
     */
    protected $statusText = '';

    /**
     * @var array<mixed>
     */
    protected $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * Constructor
     * @param mixed $content The response content
     * @param int $statusCode The response status code
     * @param array<mixed> $headers The response headers
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function __construct(
        $content = '',
        int $statusCode = 200,
        array $headers = []
    ) {
        $this->setContent($content);

        if (is_string($content)) {
            $this->body = new Body($content);
        }

        $this->statusCode = $statusCode;
        $this->headers = $this->parseHeaders($headers);
    }

    /**
     * Create response
     * @param mixed $content The response content
     * @param int $statusCode The response status code
     * @param array<mixed> $headers The response headers
     * @return Response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public static function create($content = null, int $statusCode = 200, array $headers = [])
    {
        return new static($content, $statusCode, $headers);
    }

    /**
     * Get the response content
     * @return string The response content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the response content
     * @param string|array<mixed> $content The response content
     * @return Response The current response
     */
    public function setContent($content)
    {
        if ($this->isValidContent($content)) {
            throw new \UnexpectedValueException(
                sprintf(
                    'The Response content must be a string or object implementing __toString(), "%s" given.',
                    gettype($content)
                )
            );
        }

        $this->content = $content;

        return $this;
    }

    /**
     * Parse headers
     * @param array<mixed> $headers The response headers
     * @return array<mixed> The parsed headers
     */
    public function parseHeaders($headers)
    {
        $parsedHeaders = [];

        foreach ($headers as $key => $value) {
            $key = str_replace(' ', '-', ucwords(str_replace('-', ' ', $key)));

            if (is_string($value)) {
                $parsedHeaders[$key] = explode(',', $value);
            } else {
                $parsedHeaders[$key] = $value;
            }
        }

        return $parsedHeaders;
    }

    /**
     * Get the response status code
     * @return int The response status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        if (!is_int($code) || $code < 100 || $code > 599) {
            throw new \InvalidArgumentException('Invalid HTTP status code');
        }

        $this->statusCode = $code;
        $this->statusText = $reasonPhrase;

        return $this;
    }

    /**
     * Get the response mime type
     * @return string The response mime type
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set the response mime type
     * @param string $mimeType The response mime type
     * @return Response The current response
     */
    public function setMimeType(string $mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get the response charset
     * @return string The response charset
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set the response charset
     * @param string $charset The response charset
     * @return Response The current response
     */
    public function setCharset(string $charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Get the response status text
     * @return string The response status text
     */
    public function getStatusText()
    {
        return $this->statusText;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        return $this->statusText;
    }

    /**
     * Set the response status text
     * @param string $statusText The response status text
     * @return Response The current response
     */
    public function setStatusText(string $statusText)
    {
        $this->statusText = $statusText;

        return $this;
    }

    /**
     * Get the response status text
     * @return array<mixed> The response status text
     */
    public function getStatusTexts()
    {
        return $this->statusTexts;
    }

    /**
     * Set the response status text
     * @param array<mixed> $statusTexts
     * @return Response The current response
     */
    public function setStatusTexts(array $statusTexts)
    {
        $this->statusTexts = $statusTexts;
        return $this;
    }

    /**
     * Get the response status text
     * @param int $statusCode
     * @return string The response status text
     */
    public function getStatusTextByCode(int $statusCode)
    {
        return $this->statusTexts[$statusCode];
    }

    /**
     * Set the response status text
     * @param int $statusCode
     * @param string $statusText The response status text
     * @return Response The current response
     */
    public function setStatusTextByCode(int $statusCode, string $statusText)
    {
        $this->statusTexts[$statusCode] = $statusText;
        return $this;
    }

    /**
     * Send the response with the current status code and content
     * @return Response The current response
     */
    public function send()
    {
        return $this;
    }

    /**
     * Send the response with the current status code and content
     * @return string
     */
    public function __toString()
    {
        return $this->sendContent();
    }

    /**
     * Send the response headers
     * @return void
     */
    public function sendHeaders()
    {
        if (!headers_sent()) {
            foreach ($this->headers as $name) {
                header($name . ': ' . $this->getHeaderLine($name));
            }

            header('Content-Type: ' . $this->mimeType . '; charset=' . $this->charset);
        }
    }

    /**
     * Send the response content
     * @return mixed The response content
     */
    public function sendContent()
    {
        http_response_code($this->statusCode);

        if (is_array($this->content)) {
            $this->setMimeType('application/json');
            $this->setContent(json_encode($this->content));
        }

        $this->sendHeaders();

        return $this->content;
    }

    /**
     * Verify if the content is valid
     * @param mixed $content The content to verify
     * @return bool
     */
    private function isValidContent($content)
    {
        return $content !== null
            && !is_string($content)
            && !is_numeric($content)
            && !is_array($content)
            && !is_callable([$content, '__toString']);
    }

    /**
     * Send the response with the current status code and content
     * @return void
     */
    public function __invoke()
    {
        $this->send();
    }
}
