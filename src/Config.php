<?php

namespace Awesome;

use Exception;

/**
 * Class Config
 * @package Awesome
 * @author Juan Felipe Rivera G
 */
class Config
{
    /**
     * Config params
     * @var array<mixed>
     */
    protected array $params;

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
     * Config constructor.
     * @return void
     */
    public function __construct()
    {
        $this->params = [];

        $app = App::getInstance();
        $configPath = $app->getConfigPath();

        if (!is_dir($configPath)) {
            throw new Exception('Config directory not found');
        }

        foreach (glob($configPath . '/*.php') as $filename) {
            $file = pathinfo($filename, PATHINFO_FILENAME);
            $config = require $filename;
            if (is_array($config)) {
                $this->params[$file] = $config;
            } else {
                throw new Exception('Invalid config file: ' . $filename);
            }
        }
    }
}
