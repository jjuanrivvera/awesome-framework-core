<?php

use Awesome\App;
use Awesome\Http\Request;

/**
 * Dummy function used to test
 * @param int $a
 * @param int $b
 * @return int
 */
function add(int $a, int $b): int
{
    return $a + $b;
}

/**
 * Get environment variable or default
 * @param string $key     Environment variable name
 * @param mixed|null $default Default value
 * @return mixed           Environment variable value or default
 */
function env(string $key, mixed $default = null): mixed
{
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }

    return $default;
}

/**
 * Get container instance
 * @param string|null $class Class to resolve
 * @param array<mixed> $parameters Parameters to pass to the constructor
 * @return mixed|DI\Container
 * @throws \DI\DependencyException
 * @throws \DI\NotFoundException
 */
function container(string $class = null, array $parameters = []): mixed
{
    if (is_null($class)) {
        return App::getContainer();
    }

    return App::getContainer()->make($class, $parameters);
}

/**
 * Get or set config value
 * @param string|null $key Config key
 * @return mixed|void
 * @throws \DI\DependencyException
 * @throws \DI\NotFoundException
 */
function config(string $key = null)
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
function str_replace_first(string $search, string $replace, string $subject): string
{
    return implode($replace, explode($search, $subject, 2));
}

/**
 * Resolve method dependencies
 * @param \ReflectionParameter[] $params
 * @param Request $request
 * @return array<mixed>
 * @throws \DI\DependencyException
 * @throws \DI\NotFoundException
 * @throws ReflectionException
 */
function resolve_method_dependencies(array $params, Request $request): array
{
    $dependencies = [];

    foreach ($params as $param) {
        $dependency = $param->getType();

        if ($dependency !== null) {
            $dependencies[] = container($dependency->getName());
            continue;
        }

        $params = $request->getRouteParams();

        if (isset($params[$param->name])) {
            $dependencies[] = $params[$param->name];
        } else {
            $dependencies[] = $param->getDefaultValue() ?? null;
        }
    }

    return $dependencies;
}
