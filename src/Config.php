<?php

namespace Awesome;

use Exception;
use Awesome\Interfaces\ConfigInterface;

/**
 * Class Config
 * @package Awesome
 * @author Juan Felipe Rivera G
 */
class Config implements ConfigInterface
{
    /**
     * Config params
     * @var array<mixed>
     */
    protected array $params;

    /**
     * Config path
     * @var string
     */
    protected string $configPath;

    /**
     * Config constructor.
     * @param string|null $configPath
     * @return void
     */
    public function __construct(string $configPath = null)
    {
        $this->params = [];
        $this->configPath = $configPath ?? dirname(__DIR__) . '/config';
        $this->loadConfig();
    }

    /**
     * Get config value
     * @param string $key Config key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        $keys = explode('.', $key);
        $value = $this->params;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }

            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Get all config values
     * @return array<mixed>
     */
    public function all(): array
    {
        return $this->params;
    }

    /**
     * Load config
     * @return void
     */
    public function loadConfig(): void
    {
        if (!is_dir($this->configPath)) {
            throw new Exception('Config directory not found');
        }

        foreach (glob($this->configPath . '/*.php') as $filename) {
            $file = pathinfo($filename, PATHINFO_FILENAME);
            $config = require $filename;
            if (is_array($config)) {
                $this->params[$file] = $config;
            } else {
                throw new Exception('Invalid config file: ' . $filename);
            }
        }
    }

    /**
     * Get config path
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * Set config path
     * Note: This method will unset all previous config params and load new ones
     * @param string $configPath
     * @return void
     */
    public function setConfigPath(string $configPath): void
    {
        $this->params = [];
        $this->configPath = $configPath;
        $this->loadConfig();
    }
}
