<?php

use Awesome\Router;

class RouterTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {

        Router::get('/', function () {
            return 'Home';
        });

        Router::get('/test', function () {
            return 'test';
        });

        Router::get('/test2', function () {
            return 'test2';
        });
    }

    public function testGetRoutes()
    {
        $this->assertEquals(3, count(Router::getRoutes()));
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

    public function testDispatchNotFound()
    {
        $uri = new \Awesome\Http\Uri(
            host: 'localhost',
            port: null,
            path: '/test3',
            query: '',
            scheme: 'http'
        );

        $request = new \Awesome\Http\Request(
            method: 'GET',
            headers: [],
            body: new \Awesome\Http\Body(''),
            uri: $uri
        );

        $this->expectException(\Awesome\Exceptions\NotFoundException::class);
        Router::dispatch($request);
    }

    public function testDispatchControllerNotFound()
    {
        Router::get('/test3', 'TestController@test');

        $uri = new \Awesome\Http\Uri(
            host: 'localhost',
            port: null,
            path: '/test3',
            query: '',
            scheme: 'http'
        );

        $request = new \Awesome\Http\Request(
            method: 'GET',
            headers: [],
            body: new \Awesome\Http\Body(''),
            uri: $uri
        );

        $this->expectException(\Awesome\Exceptions\ControllerNotFoundException::class);
        Router::dispatch($request);
    }

    public function testDispatchHome()
    {
        $uri = new \Awesome\Http\Uri(
            host: 'localhost',
            port: null,
            path: '/',
            query: '',
            scheme: 'http'
        );

        $request = new \Awesome\Http\Request(
            method: 'GET',
            headers: [],
            body: new \Awesome\Http\Body(''),
            uri: $uri
        );

        $this->assertEquals('Home', Router::dispatch($request));
    }
}
