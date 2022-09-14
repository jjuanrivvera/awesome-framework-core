<?php

use Awesome\App;

/**
 * Dummy function used to test
 */
function add($a, $b)
{
    return $a + $b;
}

/**
 * Get environment variable or default
 * @param  string $key     Environment variable name
 * @param  mixed  $default Default value
 * @return mixed           Environment variable value or default
 */
function env($key, $default = null)
{
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }

    return $default;
}

/**
 * Get container instance
 * @param string|null $class Class to resolve
 * @param array $parameters Parameters to pass to the constructor
 * @return mixed|DI\Container
 * @throws \DI\DependencyException
 * @throws \DI\NotFoundException
 */
function container($class = null, $parameters = [])
{
    if (is_null($class)) {
        return App::getContainer();
    }

    return App::getContainer()->make($class, $parameters);
}

/**
 * Get or set config value
 * @param string $key Config key
 * @return mixed|void
 * @throws \DI\DependencyException
 * @throws \DI\NotFoundException
 */
function config($key = null)
{
    $config = container('Awesome\Config');

    if (is_null($key)) {
        return $config;
    }
    
    return $config->get($key);
}
