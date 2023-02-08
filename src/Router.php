<?php

namespace Awesome;

use Awesome\Exceptions\NotFoundException;
use Awesome\Exceptions\ControllerNotFoundException;

/**
 * Router
 * @package Awesome
 * @author Juan Felipe Rivera G
 */
class Router
{
    /**
     * Array of routes
     * @var Route[]
     */
    protected static $routes = [];

    /**
     * Parameters from the matched route
     * @var array<mixed>
     */
    protected static $params = [];

    /**
     * Request
     * @var Request
     */
    protected static $request;

    /**
     * Add a route to the routing table
     * @param string $path The route URL
     * @param string|callable $action The route callback action
     * @param string $method The request method
     * @return void
     * @throws \Throwable
     */
    public static function add($path, $action, $method = 'GET')
    {
        $regexPath = self::buildRegexPath($path);
        $callback = null;
        $params = [];

        if (is_callable($action)) {
            $callback = $action;
        } else {
            $params = explode('@', $action);

            if (count($params) != 2) {
                throw new \Exception('Invalid action');
            }

            $params = [
                'controller' => $params[0],
                'action' => $params[1]
            ];
        }

        $route = new Route(
            path: $path,
            method: $method,
            callback: $callback,
            regexPath: $regexPath,
            params: $params
        );

        self::$routes[] = $route;
    }

    /**
     * Build Regex Path
     * @param string $path
     * @return string|string[]|null
     */
    public static function buildRegexPath($path)
    {
        // Root path to empty string
        if ($path === '/') {
            $path = '';
        }

        // Convert the path to a regular expression: escape forward slashes
        $path = preg_replace('/\//', '\\/', $path);

        // Convert variables e.g. {controller}
        $path = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $path);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $path = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $path);

        // Add start and end delimiters, and case insensitive flag
        $path = '/^' . $path . '$/i';

        return $path;
    }

    /**
     * Add a get route to the routing table
     * @param string $route The route URL
     * @param array<mixed> $params Parameters (controller, action, etc.)
     * @return void
     * @throws \Throwable
     */
    public static function get($route, $params = [])
    {
        self::add($route, $params, 'GET');
    }

    /**
     * Add a post route to the routing table
     * @param string $route The route URL
     * @param array<mixed> $params Parameters (controller, action, etc.)
     * @return void
     * @throws \Throwable
     */
    public static function post($route, $params = [])
    {
        self::add($route, $params, 'POST');
    }

    /**
     * Add a put route to the routing table
     * @param string $route The route URL
     * @param array<mixed> $params Parameters (controller, action, etc.)
     * @return void
     * @throws \Throwable
     */
    public static function put($route, $params = [])
    {
        self::add($route, $params, 'PUT');
    }

    /**
     * Add a delete route to the routing table
     * @param string $route The route URL
     * @param array<mixed> $params Parameters (controller, action, etc.)
     * @return void
     * @throws \Throwable
     */
    public static function delete($route, $params = [])
    {
        self::add($route, $params, 'DELETE');
    }

    /**
     * Add a patch route to the routing table
     * @param string $route The route URL
     * @param array<mixed> $params Parameters (controller, action, etc.)
     * @return void
     * @throws \Throwable
     */
    public static function patch($route, $params = [])
    {
        self::add($route, $params, 'PATCH');
    }

    /**
     * Add a options route to the routing table
     * @param string $route The route URL
     * @param array<mixed> $params Parameters (controller, action, etc.)
     * @return void
     * @throws \Throwable
     */
    public static function options($route, $params = [])
    {
        self::add($route, $params, 'OPTIONS');
    }

    /**
     * Get all the routes from the routing table
     * @return array<Route>
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * Match the route to the routes in the routing table, setting the params
     * property if a route is found.
     * @param string $url The route URL
     * @param Request $request The request object
     * @return Route|bool The route object
     */
    public static function match(string $url, Request $request)
    {
        foreach (self::$routes as $route) {
            $match = preg_match($route->getRegexPath(), $url, $matches);
            $method  = $request->getMethod();

            if ($match && $method == $route->getMethod()) {
                // Get named capture group values
                $params = [];

                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                $request->setRouteParams($params);

                return $route;
            }
        }

        return false;
    }

    /**
     * Get the currently matched parameters
     * @return array<mixed>
     */
    public static function getParams()
    {
        return self::$params;
    }

    /**
     * Dispatch the route, creating the controller object and running the action method
     * @return mixed
     * @throws \Exception
     */
    public static function dispatch(Request $request = null)
    {
        $request = $request ?? container()->get('Awesome\Request');
        $uri = $request->getUri();

        if (str_starts_with($uri, '/')) {
            $uri = substr($uri, 1);
        }

        $url = self::removeQueryStringVariables($uri);
        $route = self::match($url, $request);

        if (!$route) {
            throw new NotFoundException('Page not found', 404);
        }

        if ($route->hasCallable()) {
            // get callback args
            $args = (new \ReflectionFunction($route->getCallback()))->getParameters();
            // resolve dependencies
            $args = resolve_method_dependencies($args, $request);

            return call_user_func_array($route->getCallback(), $args);
        }

        // extract controller and action from route
        list('controller' => $controller, 'action' => $action) = $route->getParams();
        $controller = self::getNamespace() . $controller;

        if (!class_exists($controller)) {
            throw new ControllerNotFoundException($controller);
        }

        $controller_instance = container($controller);

        // add a suffix to the action name in order to execute the _call magic method
        $action = $action . Controller::FUNCTIONS_SUFFIX;

        return $controller_instance->$action();
    }

    /**
     * Remove the query string variables from the URL (if any). As the full
     * query string is used for the route, any variables at the end will need
     * to be removed before the route is matched to the routing table. For
     * example:
     *
     *   URL                           $_SERVER['QUERY_STRING']  Route
     *   -------------------------------------------------------------------
     *   localhost                     ''                        ''
     *   localhost/?                   ''                        ''
     *   localhost/?page=1            page=1                    ''
     *   localhost/posts?page=1      posts/?page=1            posts
     *   localhost/posts/index        posts/index               posts/index
     *   localhost/posts/index?page=2  posts/index?page=2      posts/index
     *
     * A URL of the format localhost/?page (one variable name, no value) won't
     * work however. (NB. The .htaccess file converts the first ? to a & when
     * it's passed through to the $_SERVER variable).
     *
     * @param string $url The full URL
     * @return string The URL with the query string variables removed
     */
    private static function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    /**
     * Get the namespace for the controller class. The namespace defined in the
     * route parameters is added if present.
     * @return string The request URL
     */
    private static function getNamespace()
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', self::$params)) {
            $namespace .= self::$params['namespace'] . '\\';
        }

        return $namespace;
    }
}
