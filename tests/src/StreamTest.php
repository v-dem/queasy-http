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

    public function testStringConstructorArgument()
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
        $this->assertEquals('ABCDE', (string) $stream);
    }

    public function testStringConstructorArgumentWrite()
    {
        $stream = new Stream('Hello, World!');
        $stream->write('ABCDE');

        $this->assertEquals(13, $stream->getSize());
        $this->assertEquals(', World!', $stream->getContents());
        $this->assertEquals('ABCDE, World!', (string) $stream);
    }
}

