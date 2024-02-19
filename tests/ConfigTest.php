<?php

use Awesome\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected $config;

    protected function setUp(): void
    {
        $this->config = new Config('./config/');
    }
    
    public function testConfig(): void
    {
        $this->assertInstanceOf(Config::class, $this->config);
    }

    public function testConfigHasParams(): void
    {
        $this->assertIsArray($this->config->get('app'));
    }

    public function testConfigHasName(): void
    {
        $this->assertEquals('Awesome Framework', $this->config->get('app.name'));
    }
}
