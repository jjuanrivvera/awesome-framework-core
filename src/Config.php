<?php

namespace Awesome;

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

    public function __construct()
    {
        $configParams = [];

        foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/../config/*.php') as $filename) {
            $file = pathinfo($filename, PATHINFO_FILENAME);
            $configParams[$file] = require $filename;
        }

        $this->params = $configParams;
    }
}
