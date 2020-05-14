<?php

/*
 * Queasy PHP Framework - HTTP - Tests
 *
 * (c) Vitaly Demyanenko <vitaly_demyanenko@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace queasy\http\tests;

use PHPUnit\Framework\TestCase;

use queasy\http\Stream;

use InvalidArgumentException;
use RuntimeException;

class StreamTest extends TestCase
{
    public function testNoConstructorArguments()
    {
        $stream = new Stream();

        $this->assertTrue($stream->isSeekable());
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());
        $this->assertEquals(0, $stream->getSize());
        $this->assertEquals('', $stream->getContents());
    }

    public function testScalarConstructorArgument()
    {
        $stream = new Stream(123);

        $this->assertEquals('123', $stream->getContents());
    }

    public function testStringConstructorArgument()
    {
        $stream = new Stream('Hello, World!');

        $this->assertTrue($stream->isSeekable());
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());
        $this->assertEquals(13, $stream->getSize());
        $this->assertEquals('Hello, World!', $stream->getContents());
    }

    public function testResourceConstructorArgument()
    {
        $stream = new Stream('Hello, World!');

        $this->assertTrue($stream->isSeekable());
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());
        $this->assertEquals(13, $stream->getSize());
        $this->assertEquals('Hello, World!', $stream->getContents());
    }

    public function testNoConstructorArgumentsWrite()
    {
        $stream = new Stream();
        $stream->write('ABCDE');

        $this->assertEquals(5, $stream->getSize());
        $this->assertEquals('', $stream->getContents());
        $this->assertEquals('ABCDE', $stream->__toString());
    }

    public function testStringConstructorArgumentWrite()
    {
        $stream = new Stream('Hello, World!');
        $stream->write('ABCDE');

        $this->assertEquals(13, $stream->getSize());
        $this->assertEquals(', World!', $stream->getContents());
        $this->assertEquals('ABCDE, World!', (string) $stream);
    }

    public function testSeek()
    {
        $stream = new Stream('Hello, World!');
        $stream->seek(7);

        $this->assertEquals('World!', $stream->getContents());
    }

    public function testTell()
    {
        $stream = new Stream('Hello, World!');
        $stream->write('ABCDE');

        $this->assertEquals(5, $stream->tell());
    }

    public function testDetach()
    {
        $stream = new Stream('Hello, World!!!');
        $resource = $stream->detach();

        $this->assertTrue(is_resource($resource));
    }

    public function testClose()
    {
        $stream = new Stream('Hello, World!!!');
        $stream->close();
        $resource = $stream->detach();

        $this->assertFalse(is_resource($resource));
    }

    public function testGetMetadata()
    {
        $stream = new Stream('Hello, World!!!');
        $meta = $stream->getMetadata();

        $this->assertEquals('PHP', $meta['wrapper_type']);
        $this->assertEquals('TEMP', $meta['stream_type']);
        $this->assertEquals('w+b', $meta['mode']);
        $this->assertEquals(0, $meta['unread_bytes']);
        $this->assertEquals(1, $meta['seekable']);
        $this->assertEquals('php://temp', $meta['uri']);
    }

    public function testGetMetadataKey()
    {
        $stream = new Stream('Hello, World!!!');
        $mode = $stream->getMetadata('mode');

        $this->assertEquals('w+b', $mode);
    }

    public function testGetMetadataKeyNotExists()
    {
        $stream = new Stream('Hello, World!!!');

        $this->assertNull($stream->getMetadata('not_exists'));
    }

    public function testStreamNotWritable()
    {
        $stream = new Stream(STDIN);

        $this->assertFalse($stream->isWritable());

        $this->expectException(RuntimeException::class);

        $stream->write('232');
    }

    public function testStreamNotReadable()
    {
        $stream = new Stream(STDOUT);

        $this->assertFalse($stream->isReadable());

        $this->expectException(RuntimeException::class);

        $result = $stream->read(5);
    }

    public function testStreamNotSeekable()
    {
        $stream = new Stream(STDOUT);

        $this->assertFalse($stream->isSeekable());

        $this->expectException(RuntimeException::class);

        $stream->seek(5);
    }

    public function testEof()
    {
        $stream = new Stream('Hello, World!!!');

        $this->assertFalse($stream->eof());

        $stream->read(16);

        $this->assertTrue($stream->eof());
    }
}

