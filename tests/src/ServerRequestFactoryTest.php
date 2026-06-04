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

use queasy\http\ServerRequestFactory;

use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactoryTest extends TestCase
{
    public function testMain()
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('GET', 'http://127.0.0.1');

        $this->assertTrue($request instanceof ServerRequestInterface);
    }
}

