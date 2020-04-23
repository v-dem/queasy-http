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

use queasy\http\Uri;

use Exception;
use InvalidArgumentException;
use ArgumentCountError;

class UriTest extends TestCase
{
    public function testEmpty()
    {
        $this->expectException(ArgumentCountError::class);

        $uri = new Uri();
    }

    public function testEmptyString()
    {
        try {
            $uri = new Uri('');
        } catch (Exception $e) {
            $this->fail();
        }

        $this->assertNotNull($uri);
    }

    public function testNumeric()
    {
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri(123);
    }

    public function testFullString()
    {
        $uri = new Uri('http://john.doe:32167@example.com:8080/path/to/index.php?a=123&b=sdasad#here');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('john.doe:32167@example.com:8080', $uri->getAuthority());
        $this->assertEquals('john.doe:32167', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('/path/to/index.php', $uri->getPath());
        $this->assertEquals('a=123&b=sdasad', $uri->getQuery());
        $this->assertEquals('here', $uri->getFragment());
    }
}

