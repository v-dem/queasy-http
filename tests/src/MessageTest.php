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

use queasy\http\Message;
use queasy\http\Stream;

class MessageTest extends TestCase
{
    public function testProtocolVersion()
    {
        $message = new Message('1.1', [], new Stream());

        $this->assertEquals('1.1', $message->getProtocolVersion());
    }

    public function testWithProtocolVersion()
    {
        $message = new Message('1.1', [], new Stream());
        $clonedMessage = $message->withProtocolVersion('1.0');

        $this->assertNotSame($clonedMessage, $message);
        $this->assertEquals('1.0', $clonedMessage->getProtocolVersion());
        $this->assertEquals('1.1', $message->getProtocolVersion());
    }

    public function testGetHeaders()
    {
        $headers = [
            'header1' => [12, 33, 'abcd'],
            'Header2' => ['aa', 'bb']
        ];

        $message = new Message('1.1', $headers, new Stream());

        $this->assertEquals($message->getHeaders(), $headers);
    }

    public function testHasHeader()
    {
        $headers = [
            'header1' => [12, 33, 'abcd'],
            'Header2' => ['aa', 'bb']
        ];

        $message = new Message('1.1', $headers, new Stream());

        $this->assertTrue($message->hasHeader('header1'));
        $this->assertTrue($message->hasHeader('header2'));
        $this->assertTrue($message->hasHeader('Header2'));
        $this->assertTrue($message->hasHeader('HEADER1'));
        $this->assertFalse($message->hasHeader('header3'));
    }

    public function testGetHeader()
    {
        $headers = [
            'header1' => [12, 33, 'abcd'],
            'Header2' => ['aa', 'bb']
        ];

        $message = new Message('1.1', $headers, new Stream());

        $this->assertEquals([12, 33, 'abcd'], $message->getHeader('header1'));
        $this->assertEquals([12, 33, 'abcd'], $message->getHeader('HEADER1'));
        $this->assertEquals(['aa', 'bb'], $message->getHeader('HEADER2'));
        $this->assertEquals([], $message->getHeader('header3'));
    }

    public function testGetHeaderLine()
    {
        $headers = [
            'header1' => [12, 33, 'abcd'],
            'Header2' => ['aa', 'bb']
        ];

        $message = new Message('1.1', $headers, new Stream());

        $this->assertEquals('12,33,abcd', $message->getHeaderLine('Header1'));
        $this->assertEquals('aa,bb', $message->getHeaderLine('header2'));
        $this->assertEquals('', $message->getHeaderLine('header3'));
    }
}

