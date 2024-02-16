<?php

use Awesome\App;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
        $this->app = App::getInstance();
    }

    public function testApp(): void
    {
        
        $this->assertInstanceOf(App::class, $this->app);
    }

    public function testAppHasContainer(): void
    {
        $this->assertInstanceOf(
            \DI\Container::class,
            $this->app->getContainer()
        );
    }
}
