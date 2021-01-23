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

        $this->assertEquals($headers, $message->getHeaders());
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

    public function testWithHeader()
    {
        $headers = [
            'header1' => [12, 33, 'abcd'],
            'Header2' => ['aa', 'bb']
        ];

        $message = new Message('1.1', $headers, new Stream());
        $clonedMessage = $message->withHeader('Header1', [7]);

        $this->assertNotSame($clonedMessage, $message);
        $this->assertTrue($clonedMessage->hasHeader('header1'));
        $this->assertEquals([7], $clonedMessage->getHeader('HEADER1'));
    }

    public function testWithAddedHeader()
    {
        $headers = [
            'header1' => [12, 33, 'abcd'],
            'Header2' => ['aa', 'bb']
        ];

        $message = new Message('1.1', $headers, new Stream());
        $clonedMessage = $message->withAddedHeader('Header1', [7]);

        $this->assertNotSame($clonedMessage, $message);
        $this->assertTrue($clonedMessage->hasHeader('header1'));
        $this->assertEquals([12, 33, 'abcd', 7], $clonedMessage->getHeader('HEADER1'));
    }


    public function testWithAddedHeaderSingleValue()
    {
        $headers = [
            'header1' => [12, 33, 'abcd'],
            'Header2' => ['aa', 'bb']
        ];

        $message = new Message('1.1', $headers, new Stream());
        $clonedMessage = $message->withAddedHeader('Header3', 7);

        $this->assertNotSame($clonedMessage, $message);
        $this->assertTrue($clonedMessage->hasHeader('header3'));
        $this->assertEquals([7], $clonedMessage->getHeader('HEADER3'));
    }

    public function testWithNewAddedHeader()
    {
        $headers = [
            'header1' => [12, 33, 'abcd'],
            'Header2' => ['aa', 'bb']
        ];

        $message = new Message('1.1', $headers, new Stream());
        $clonedMessage = $message->withAddedHeader('Header3', [7]);

        $this->assertNotSame($clonedMessage, $message);
        $this->assertTrue($clonedMessage->hasHeader('header3'));
        $this->assertEquals([7], $clonedMessage->getHeader('HEADER3'));
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

    public function testWithoutHeader()
    {
        $headers = [
            'header1' => [12, 33, 'abcd'],
            'Header2' => ['aa', 'bb']
        ];

        $message = new Message('1.1', $headers, new Stream());
        $clonedMessage = $message->withoutHeader('Header1', [7]);

        $this->assertNotSame($clonedMessage, $message);
        $this->assertTrue($clonedMessage->hasHeader('header2'));
        $this->assertFalse($clonedMessage->hasHeader('header1'));
    }

}

