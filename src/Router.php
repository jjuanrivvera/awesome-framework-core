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
     * @var array
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
     * @param string $route The route URL
     * @param array $params Parameters (controller, action, etc.)
     * @return void
     */
    public static function add($route, $params = [])
    {
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        
        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';

        self::$routes[$route] = $params;
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
     * @return boolean  true if a match found, false otherwise
     */
    public static function match($url)
    {
        // Match to the fixed URL format /controller/action
        //$reg_exp = '/^(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)$/';
        
        foreach (self::$routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                // Get named capture group values

                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                self::$params = $params;
                self::$request->setRouteParams($params);

                return true;
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
     * @return void
     */
    public static function dispatch($url)
    {
        $url = self::removeQueryStringVariables($url);
        
        if (self::match($url)) {
            $controller = self::$params['controller'];

            if (strpos($controller, 'Controller') === false) {
                $controller .= 'Controller';
            }

            $controller = self::convertToStudlyCaps($controller);
            $controller = self::getNamespace() . $controller;
            
            if (class_exists($controller)) {
                $controller_object = container($controller);

                $action = self::$params['action'];
                $action = self::convertToCamelCase($action);
                
                if (preg_match('/action$/i', $action) == 0) {
                    $controller_object->$action();
                } else {
                    throw new \Exception("Method $action (in controller $controller) not found");
                }
            } else {
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            throw new \Exception('Page not found', 404);
        }
    }

    /**
     * Convert the string with hyphens to StudlyCaps,
     * e.g. post-authors => PostAuthors
     * @param string $string The string to convert
     * @return string
     */
    protected static function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convert the string with hyphens to camelCase,
     * e.g. add-new => addNew
     * @param string $string The string to convert
     * @return string
     */
    protected static function convertToCamelCase($string)
    {
        return lcfirst(self::convertToStudlyCaps($string));
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
