<?php

namespace Awesome;

use Awesome\View;
use Awesome\Response;

/**
 * Base controller
 * @package    Awesome
 */
abstract class Controller
{
    /**
     * Function Suffix
     * @var string
     */
    public const FUNCTIONS_SUFFIX = 'Action';

    /**
     * Request
     * @var Request
     */
    protected $request;

    /**
     * Controller constructor
     * @param Request $request Parameters from the route
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     * @param string $name Method name
     * @param array $args Arguments passed to the method
     * @return void
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $method = str_replace(self::FUNCTIONS_SUFFIX, '', $name);

        $reflection = new \ReflectionMethod(get_class($this), $method);

        $params = $reflection->getParameters();

        $args = $this->resolveMethodDependencies($params, $args);

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                $response = call_user_func_array([$this, $method], $args);
                $this->after($response);
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Resolve method dependencies
     * @param array $params
     * @param array $args
     * @return array
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function resolveMethodDependencies($params, $args)
    {
        $dependencies = [];

        foreach ($params as $param) {
            $dependency = $param->getClass();

            if ($dependency === null) {
                $params = $this->request->getRouteParams();
                if (isset($params[$param->name])) {
                    $dependencies[] = $params[$param->name];
                } else {
                    $dependencies[] = array_shift($args);
                }
            } else {
                $dependencies[] = container($dependency->name);
            }
        }

        return $dependencies;
    }

    /**
     * Before filter - called before an action method.
     * @return void
     */
    protected function before()
    {
    }

    /**
     * After filter - called after an action method.
     * @param Response|View|string|null $response Response object
     * @return void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function after($response)
    {
        if ($response instanceof Response) {
            echo $response;
        } elseif ($response instanceof View) {
            echo $response->render();
        } else {
            echo new Response($response);
        }
    }
}
