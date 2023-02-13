<?php

namespace Awesome\Http;

use Psr\Http\Message\StreamInterface;

class Body implements StreamInterface
{
    /**
     * Stream
     * @var resource|null
     */
    protected $stream;

    /**
     * Body constructor
     * @param resource|string $content
     * @return void
     */
    public function __construct($content)
    {
        if (is_string($content)) {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $content);
            rewind($stream);
            $this->stream = $stream;
            return;
        }

        $this->stream = $content;
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     * @return string
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        $stream = $this->stream;
        fclose($stream);

        $this->stream = null;
    }

    /**
     * Separates any underlying resources from the stream.
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $stream = $this->stream;
        $this->stream = null;

        return $stream;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        $stream = $this->stream;
        $stats = fstat($stream);

        return $stats['size'];
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        $stream = $this->stream;
        $position = ftell($stream);

        if ($position === false) {
            throw new \RuntimeException('Unable to get position of stream');
        }

        return $position;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof(): bool
    {
        $stream = $this->stream;

        return feof($stream);
    }

    /**
     * Returns whether the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
        $stream = $this->stream;
        $meta = stream_get_meta_data($stream);

        return $meta['seekable'];
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET): int
    {
        $stream = $this->stream;
        $result = fseek($stream, $offset, $whence);

        if ($result === -1) {
            throw new \RuntimeException(
                'Unable to seek to stream position ' . $offset . ' with whence ' . var_export($whence, true)
            );
        }

        return $result;
    }

    /**
     * Seek to the beginning of the stream.
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind(): bool
    {
        $stream = $this->stream;
        $result = rewind($stream);

        if ($result === false) {
            throw new \RuntimeException('Unable to rewind stream');
        }

        return true;
    }

    /**
     * Returns whether the stream is writable.
     * @return bool
     */
    public function isWritable(): bool
    {
        $stream = $this->stream;
        $meta = stream_get_meta_data($stream);

        return $meta['mode'] === 'w' ||
            $meta['mode'] === 'w+' ||
            $meta['mode'] === 'rw' ||
            $meta['mode'] === 'r+' ||
            $meta['mode'] === 'x+' ||
            $meta['mode'] === 'c+' ||
            $meta['mode'] === 'wb' ||
            $meta['mode'] === 'w+b' ||
            $meta['mode'] === 'r+b' ||
            $meta['mode'] === 'x+b' ||
            $meta['mode'] === 'c+b' ||
            $meta['mode'] === 'wb+' ||
            $meta['mode'] === 'a' ||
            $meta['mode'] === 'a+' ||
            $meta['mode'] === 'ab' ||
            $meta['mode'] === 'ab+' ||
            $meta['mode'] === 'a+b';
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string): int
    {
        $stream = $this->stream;
        $result = fwrite($stream, $string);

        if ($result === false) {
            throw new \RuntimeException('Unable to write to stream');
        }

        return $result;
    }

    /**
     * Returns whether the stream is readable.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        $stream = $this->stream;
        $meta = stream_get_meta_data($stream);

        return $meta['mode'] === 'r' ||
            $meta['mode'] === 'r+' ||
            $meta['mode'] === 'rw' ||
            $meta['mode'] === 'w+' ||
            $meta['mode'] === 'x+' ||
            $meta['mode'] === 'c+' ||
            $meta['mode'] === 'rb' ||
            $meta['mode'] === 'r+b' ||
            $meta['mode'] === 'w+b' ||
            $meta['mode'] === 'x+b' ||
            $meta['mode'] === 'c+b' ||
            $meta['mode'] === 'rb+' ||
            $meta['mode'] === 'a' ||
            $meta['mode'] === 'a+' ||
            $meta['mode'] === 'ab' ||
            $meta['mode'] === 'ab+' ||
            $meta['mode'] === 'a+b';
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length): string
    {
        $stream = $this->stream;
        $result = fread($stream, $length);

        if ($result === false) {
            throw new \RuntimeException('Unable to read from stream');
        }

        return $result;
    }

    /**
     * Returns the remaining contents in a string
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents(): string
    {
        $stream = $this->stream;
        $result = stream_get_contents($stream);

        if ($result === false) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        return $result;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null): mixed
    {
        $stream = $this->stream;
        $meta = stream_get_meta_data($stream);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}
