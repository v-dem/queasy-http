<?php

namespace queasy\http;

use Psr\Http\Message\StreamInterface;

use Exception;
use RuntimeException;

/**
 * Describes a data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 */
class Stream implements StreamInterface
{
    const BUFFER_SIZE = 65536;

    private static $READABLE_MODES = array(
        'r', 'r+', 'w+', 'a+', 'x+', 'c+'
    );

    private static $WRITABLE_MODES = array(
        'r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'
    );

    private $resource;

    private $bufferSize = self::BUFFER_SIZE;

    private $isSeekable;

    private $isWritable;

    private $isReadable;

    private $meta;

    public function __construct($resource = null, $bufferSize = null)
    {
        if (null !== $bufferSize) {
            $this->bufferSize = $bufferSize;
        }

        if (is_resource($resource)) {
            $this->resource = $resource;
        } else {
            $this->resource = fopen('php://temp', 'w+');
            if (is_string($resource)) {
                $this->write($resource);
                $this->rewind();
            }
        }

        $this->meta = stream_get_meta_data($this->resource);

        $this->isSeekable = $this->meta['seekable'];
        $this->isWritable = in_array($this->meta['mode'], static::$WRITABLE_MODES);
        $this->isReadable = in_array($this->meta['mode'], static::$READABLE_MODES);
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        fclose($this->resource);
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $resource = $this->resource;

        $this->resource = null;

        return $resource;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        $stat = fstat($this->resource);
        if (!$stat) {
            return null;
        }

        return $stat['size'];
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        $result = ftell($this->resource);
        if (false === $result) {
            throw new RuntimeException('Cannot get resource position.');
        }

        return $result;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return feof($this->resource);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->isSeekable;
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
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->isSeekable) {
            throw new RuntimeException('Resource is not seekable.');
        }

        $result = fseek($this->resource, $offset, $whence);
        if (-1 === $result) {
            throw new RuntimeException('Error occured while writing to set resource position.');
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this->isWritable;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (!$this->isWritable) {
            throw new RuntimeException('Resource is not writable.');
        }

        $result = fwrite($this->resource, $string);
        if (false === $result) {
            throw new RuntimeException('Error occured while writing to resource.');
        }
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return $this->isReadable;
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
    public function read($length)
    {
        $result = fread($this->resource, $length);
        if (false === $result) {
            throw new RuntimeException('Error occured while reading from resource.');
        }

        return $result;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        $result = '';
        while ($buffer = $this->read($this->bufferSize)) {
            $result .= $buffer;
        }

        return $result;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        return is_null($key)
            ? $this->meta
            : (array_key_exists($key, $this->meta)
                ? $this->meta[$key]
                : null);
    }
}
