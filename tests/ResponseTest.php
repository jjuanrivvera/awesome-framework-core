<?php

use Awesome\Http\Body;
use Awesome\Http\Response;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    protected $response;

    protected function setUp(): void
    {
        $response = new Response(
            content: 'test',
            statusCode: 200,
            headers: [],
        );

        $this->response = $response;
    }

    public function testGetStatusCode()
    {
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testWithStatus()
    {
        $response = $this->response->withStatus(201);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testGetReasonPhrase()
    {
        $this->assertEquals('', $this->response->getReasonPhrase());
    }

    public function testGetBody()
    {
        $this->assertEquals('test', $this->response->getBody());
    }

    public function testWithBody()
    {
        $body = new Body('test2');
        $response = $this->response->withBody($body);
        $this->assertEquals('test2', $response->getBody()->__toString());
    }

    public function testGetProtocolVersion()
    {
        $this->assertEquals('1.1', $this->response->getProtocolVersion());
    }

    public function testWithProtocolVersion()
    {
        $response = $this->response->withProtocolVersion('1.0');
        $this->assertEquals('1.0', $response->getProtocolVersion());
    }

    public function testGetHeaders()
    {
        $this->assertEquals([], $this->response->getHeaders());
    }

    public function testHasHeader()
    {
        $this->assertFalse($this->response->hasHeader('Content-Type'));
    }

    public function testGetHeader()
    {
        $this->assertEquals([], $this->response->getHeader('Content-Type'));
    }
}
