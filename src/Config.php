<?php

namespace Awesome;

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
        $configParams = [];

        foreach (glob(App::getConfigPath()) as $filename) {
            $file = pathinfo($filename, PATHINFO_FILENAME);
            $configParams[$file] = require $filename;
        }

        $this->params = $configParams;
    }
}
