<?php

use Awesome\Http\Uri;
use Awesome\Http\Body;
use Awesome\Http\Request;
use Psr\Http\Message\UriInterface;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    protected $request;

    protected function setUp(): void
    {
        $uri = new Uri(
            host: 'localhost',
            port: null,
            path: '/test',
            query: 'test=1',
            scheme: 'http'
        );

        $body = new Body('test');

        $request = new Request(
            method: 'GET',
            headers: [],
            body: $body,
            uri: $uri
        );

        $this->request = $request;
    }

    public function testGetUriInstanceOfUriInterface()
    {
        $this->assertInstanceOf(UriInterface::class, $this->request->getUri());
    }

    public function testGetUriString()
    {
        $this->assertEquals('http://localhost/test?test=1', $this->request->getUri()->__toString());
    }

    public function testGetRequestTarget()
    {
        $this->assertEquals('/test?test=1', $this->request->getRequestTarget());
    }

    public function testWithRequestTarget()
    {
        $request = $this->request->withRequestTarget('/test2?test=2');
        $this->assertEquals('/test2?test=2', $request->getRequestTarget());
    }

    public function testGetMethod()
    {
        $this->assertEquals('GET', $this->request->getMethod());
    }

    public function testWithMethod()
    {
        $request = $this->request->withMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetProtocolVersion()
    {
        $this->assertEquals('1.1', $this->request->getProtocolVersion());
    }

    public function testWithProtocolVersion()
    {
        $request = $this->request->withProtocolVersion('1.0');
        $this->assertEquals('1.0', $request->getProtocolVersion());
    }

    public function testGetHeaders()
    {
        $this->assertEquals([], $this->request->getHeaders());
    }

    public function testHasHeader()
    {
        $this->assertFalse($this->request->hasHeader('test'));
    }

    public function testGetHeader()
    {
        $this->assertEquals([], $this->request->getHeader('test'));
    }

    public function testGetHeaderLine()
    {
        $this->assertEquals('', $this->request->getHeaderLine('test'));
    }

    public function testWithHeader()
    {
        $request = $this->request->withHeader('test', 'test');
        $this->assertEquals(['test'], $request->getHeader('test'));
    }

    public function testWithAddedHeader()
    {
        $request = $this->request->withAddedHeader('test', 'test');
        $this->assertEquals(['test'], $request->getHeader('test'));
    }

    public function testWithoutHeader()
    {
        $request = $this->request->withoutHeader('test');
        $this->assertEquals([], $request->getHeader('test'));
    }

    public function testGetBodyInstanceOfBody()
    {
        $this->assertInstanceOf(Body::class, $this->request->getBody());
    }

    public function testGetBodyString()
    {
        $this->assertEquals('test', $this->request->getBody()->__toString());
    }

    public function testGetRouteParams()
    {
        $this->assertEquals([], $this->request->getRouteParams());
    }
}
