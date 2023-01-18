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
     */
    protected $params;

    /**
     * Get config value
     * @param string $key Config key
     * @return mixed
     */
    public function get($key)
    {
        $keys = explode('.', $key);

        if (count($keys) === 0) {
            return null;
        }

        $value = $this->params[$keys[0]];

        for ($i = 1; $i < count($keys); $i++) {
            $value = $value[$keys[$i]];
        }

        return $value;
    }

    /**
     * Config constructor.
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
