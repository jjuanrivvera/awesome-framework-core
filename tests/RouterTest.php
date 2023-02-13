<?php

use Awesome\Router;

class RouterTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        Router::get('/test', function () {
            return 'test';
        });

        Router::get('/test2', function () {
            return 'test2';
        });
    }

    public function testGetRoutes()
    {
        $this->assertEquals(2, count(Router::getRoutes()));
    }

    public function testDispatch()
    {
        $uri = new \Awesome\Http\Uri(
            host: 'localhost',
            port: null,
            path: '/test',
            query: '',
            scheme: 'http'
        );

        $request = new \Awesome\Http\Request(
            method: 'GET',
            headers: [],
            body: new \Awesome\Http\Body(''),
            uri: $uri
        );

        $this->assertEquals('test', Router::dispatch($request));
    }
}
