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

    public function testWithoutUser()
    {
        $uri = new Uri('http://example.com:8080/path/to/index.php?a=123&b=sdasad#here');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('example.com:8080', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('/path/to/index.php', $uri->getPath());
        $this->assertEquals('a=123&b=sdasad', $uri->getQuery());
        $this->assertEquals('here', $uri->getFragment());
    }

    public function testWithoutPassword()
    {
        $uri = new Uri('http://john.doe@example.com:8080/path/to/index.php?a=123&b=sdasad#here');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('john.doe@example.com:8080', $uri->getAuthority());
        $this->assertEquals('john.doe', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('/path/to/index.php', $uri->getPath());
        $this->assertEquals('a=123&b=sdasad', $uri->getQuery());
        $this->assertEquals('here', $uri->getFragment());
    }

    public function testWithoutPort()
    {
        $uri = new Uri('http://john.doe@example.com/path/to/index.php?a=123&b=sdasad#here');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('john.doe@example.com', $uri->getAuthority());
        $this->assertEquals('john.doe', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals('/path/to/index.php', $uri->getPath());
        $this->assertEquals('a=123&b=sdasad', $uri->getQuery());
        $this->assertEquals('here', $uri->getFragment());
    }

    public function testWithoutScheme()
    {
        $uri = new Uri('//john.doe@example.com/path/to/index.php?a=123&b=sdasad#here');

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('john.doe@example.com', $uri->getAuthority());
        $this->assertEquals('john.doe', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals('/path/to/index.php', $uri->getPath());
        $this->assertEquals('a=123&b=sdasad', $uri->getQuery());
        $this->assertEquals('here', $uri->getFragment());
    }

    public function testWithoutFragment()
    {
        $uri = new Uri('//john.doe@example.com/path/to/index.php?a=123&b=sdasad');

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('john.doe@example.com', $uri->getAuthority());
        $this->assertEquals('john.doe', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals('/path/to/index.php', $uri->getPath());
        $this->assertEquals('a=123&b=sdasad', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testWithoutQuery()
    {
        $uri = new Uri('//john.doe@example.com/path/to/index.php');

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('john.doe@example.com', $uri->getAuthority());
        $this->assertEquals('john.doe', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals('/path/to/index.php', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testWithoutPath1()
    {
        $uri = new Uri('//john.doe@example.com/');

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('john.doe@example.com', $uri->getAuthority());
        $this->assertEquals('john.doe', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals('/', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testWithoutPath2()
    {
        $uri = new Uri('//john.doe@example.com');

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('john.doe@example.com', $uri->getAuthority());
        $this->assertEquals('john.doe', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('', $uri->getFragment());
    }

    public function testWithoutPathWithFragment()
    {
        $uri = new Uri('//john.doe@example.com#here');

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('john.doe@example.com', $uri->getAuthority());
        $this->assertEquals('john.doe', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('here', $uri->getFragment());
    }

    public function testWithPathAndFragmentOnly()
    {
        $uri = new Uri('example.com#here');

        $this->assertEquals('', $uri->getScheme());
        $this->assertEquals('', $uri->getAuthority());
        $this->assertEquals('', $uri->getUserInfo());
        $this->assertEquals('', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertEquals('example.com', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
        $this->assertEquals('here', $uri->getFragment());
    }

    public function testChangeScheme()
    {
        $uri = new Uri('http://example.com');
        $newUri = $uri->withScheme('https');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('https', $newUri->getScheme());
    }

    public function testChangeUserAndPassword()
    {
        $uri = new Uri('http://user:password@example.com');
        $newUri = $uri->withUserInfo('newuser', 'newpassword');

        $this->assertEquals('user:password', $uri->getUserInfo());
        $this->assertEquals('newuser:newpassword', $newUri->getUserInfo());
    }

    public function testChangeHost()
    {
        $uri = new Uri('http://example.com:8080/road/to/nowhere?a=12&b=7#here');
        $newUri = $uri->withHost('my.example.com');

        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals('my.example.com', $newUri->getHost());
    }

    public function testChangePort()
    {
        $uri = new Uri('http://example.com:8080/road/to/nowhere?a=12&b=7#here');
        $newUri = $uri->withPort('8888');

        $this->assertEquals('8080', $uri->getPort());
        $this->assertEquals('8888', $newUri->getPort());
    }

    public function testChangePath()
    {   // FIXME:
        $uri = new Uri('http://example.com:8080/road/to/nowhere?a=12&b=7#here');
        $newUri = $uri->withPath('/my/new/path');

        $this->assertEquals('/road/to/nowhere', $uri->getPath());
        $this->assertEquals('/my/new/path', $newUri->getPath());
    }

    public function testChangeQuery()
    {
        $uri = new Uri('http://example.com:8080/road/to/nowhere?a=12&b=7#here');
        $newUri = $uri->withQuery('x=123');

        $this->assertEquals('a=12&b=7', $uri->getQuery());
        $this->assertEquals('x=123', $newUri->getQuery());
    }

    public function testChangeFragment()
    {
        $uri = new Uri('http://example.com:8080/road/to/nowhere?a=12&b=7#here');
        $newUri = $uri->withFragment('somewhere');

        $this->assertEquals('here', $uri->getFragment());
        $this->assertEquals('somewhere', $newUri->getFragment());
    }

    public function testToStringFull()
    {
        $uri = new Uri([
            'scheme' => 'https',
            'user' => 'john.doe',
            'pass' => 'secret123',
            'port' => '8080',
            'host' => 'example.com',
            'path' => '/some/path',
            'query' => 'a=12&b=231',
            'fragment' => 'there'
        ]);

        $this->assertEquals('https://john.doe:secret123@example.com:8080/some/path?a=12&b=231#there', (string) $uri);
    }
}

