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
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $method = str_replace_first(self::FUNCTIONS_SUFFIX, '', $name);

        $reflection = new \ReflectionMethod(get_class($this), $method);

        $params = $reflection->getParameters();

        $args = resolveMethodDependencies($params, $this->request);

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                $response = call_user_func_array([$this, $method], $args);
                return $this->after($response);
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
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
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function after($response)
    {
        $finalResponse = new Response($response);

        if ($response instanceof Response) {
            $finalResponse = $response;
        } elseif ($response instanceof View) {
            $finalResponse = $response->render();
        }

        if (!App::isCli()) {
            echo $finalResponse;
            return;
        }

        return $finalResponse;
    }
}
