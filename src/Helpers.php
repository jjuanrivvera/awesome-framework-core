<?php

use Awesome\App;
use Awesome\Request;

/**
 * Dummy function used to test
 * @param $a
 * @param $b
 * @return mixed
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

/**
 * Replace first occurrence of a string
 * @param string $search
 * @param string $replace
 * @param string $subject
 * @return string
 */
function str_replace_first($search, $replace, $subject)
{
    return implode($replace, explode($search, $subject, 2));
}

/**
 * Resolve method dependencies
 * @param \ReflectionParameter[] $params
 * @param Request $request
 * @return array
 * @throws \DI\DependencyException
 * @throws \DI\NotFoundException
 * @throws ReflectionException
 */
function resolveMethodDependencies($params, Request $request)
{
    $dependencies = [];

    foreach ($params as $param) {
        $dependency = $param->getClass();

        if ($dependency === null) {
            $params = $request->getRouteParams();
            if (isset($params[$param->name])) {
                $dependencies[] = $params[$param->name];
            } else {
                $dependencies[] = $param->getDefaultValue() ?? null;
            }
        } else {
            $dependencies[] = container($dependency->name);
        }
    }

    return $dependencies;
}
