<?php

namespace Awesome;

/**
 * Router
 * @package    Awesome
 * @author     Juan Felipe Rivera G
 */
class Router
{
    /**
     * Associative array of routes (the routing table)
     * @var Route[]
     */
    protected static $routes = [];

    /**
     * Parameters from the matched route
     * @var array
     */
    protected static $params = [];

    /**
     * Request
     * @var Request
     */
    protected static $request;

    /**
     * Router constructor
     * @param Request $request Parameters from the route
     * @return void
     */
    public function __construct(Request $request)
    {
        self::$request = $request;
    }

    /**
     * Add a route to the routing table
     * @param string $uri The route URL
     * @param string|callable $action The route callback action
     * @param string $method The request method
     * @return void
     */
    public static function add($uri, $action = null, $method = 'GET')
    {
        $regexPath = self::buildRegexPath($uri);
        $path = $uri;
        $callback = null;
        $params = [];

        if (is_callable($action)) {
            $callback = $action;
        } else {
            $params = explode('@', $action);
            $params = [
                'controller' => $params[0],
                'action' => $params[1]
            ];
        }

        $route = new Route(compact('path', 'method', 'callback', 'regexPath', 'params'));
        
        self::$routes[] = $route;
    }

    /**
     * Build Regex Path
     * @param string route
     */
    public static function buildRegexPath($route)
    {
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';

        return $route;
    }

    /**
     * Add a get route to the routing table
     * @param string $route The route URL
     * @param array $params Parameters (controller, action, etc.)
     * @return void
     */
    public static function get($route, $params = [])
    {
        self::add($route, $params, 'GET');
    }

    /**
     * Add a post route to the routing table
     * @param string $route The route URL
     * @param array $params Parameters (controller, action, etc.)
     * @return void
     */
    public static function post($route, $params = [])
    {
        self::add($route, $params, 'POST');
    }

    /**
     * Add a put route to the routing table
     * @param string $route The route URL
     * @param array $params Parameters (controller, action, etc.)
     * @return void
     */
    public static function put($route, $params = [])
    {
        self::add($route, $params, 'PUT');
    }

    /**
     * Add a delete route to the routing table
     * @param string $route The route URL
     * @param array $params Parameters (controller, action, etc.)
     * @return void
     */
    public static function delete($route, $params = [])
    {
        self::add($route, $params, 'DELETE');
    }

    /**
     * Add a patch route to the routing table
     * @param string $route The route URL
     * @param array $params Parameters (controller, action, etc.)
     * @return void
     */
    public static function patch($route, $params = [])
    {
        self::add($route, $params, 'PATCH');
    }

    /**
     * Add a options route to the routing table
     * @param string $route The route URL
     * @param array $params Parameters (controller, action, etc.)
     * @return void
     */
    public static function options($route, $params = [])
    {
        self::add($route, $params, 'OPTIONS');
    }

    /**
     * Get all the routes from the routing table
     * @return array
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * Match the route to the routes in the routing table, setting the params
     * property if a route is found.
     * @param string $url The route URL
     * @return Route|bool The route object
     */
    public static function match($url)
    {
        foreach (self::$routes as $route) {
            $match = preg_match($route->getRegexPath(), $url, $matches);
            $method  = self::$request->getMethod();

            if ($match && $method == $route->getMethod()) {
                // Get named capture group values
                $params = [];
                
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                self::$request->setRouteParams($params);

                return $route;
            }
        }

        return false;
    }

    /**
     * Get the currently matched parameters
     * @return array
     */
    public static function getParams()
    {
        return self::$params;
    }

    /**
     * Dispatch the route, creating the controller object and running the action method
     * @param string $url The route URL
     * @return mixed
     * @throws \Exception
     */
    public static function dispatch($url)
    {
        $url = self::removeQueryStringVariables($url);
        $route = self::match($url);
        
        if (!$route) {
            throw new \Exception('Page not found', 404);
        }

        if ($route->hasCallable()) {
            return $route->call();
        }

        $params = $route->getParams();
        $controller = $params['controller'];
        $controller = self::getNamespace() . $controller;
        $action = $params['action'];

        if (!class_exists($controller)) {
            throw new \Exception("Controller class $controller not found");
        }

        if (!preg_match('/action$/i', $action) == 0) {
            throw new \Exception("Method $action (in controller $controller) not found");
        }

        $controller_instance = container($controller);
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
