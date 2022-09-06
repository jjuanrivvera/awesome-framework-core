<?php

/**
 * App
 * @package Awesome
 * @author  Juan Felipe Rivera G
 */

namespace Awesome;

use Exception;
use DI\Container;
use DI\ContainerBuilder;

class App
{
    /**
     * @var Container
     */
    protected static $container;

    /**
     * @var Router
     */
    protected static $router;
    
    /**
     * App Constructor
     */
    public function __construct()
    {
        $container = new Container();

        $env = $_ENV['APP_ENV'] ?: 'production';

        if ($env === 'production') {
            $builder = new ContainerBuilder();
            $builder->enableCompilation(dirname(__DIR__) . '/tmp');
            $builder->writeProxiesToFile(true, dirname(__DIR__) . '/tmp/proxies');
            $container = $builder->build();
        }

        self::$container = $container;
    }

    /**
     * Get container instance
     * @return Container
     */
    public static function getContainer()
    {
        return self::$container;
    }

    /**
     * Load routes
     * @return void
     */
    public static function loadRoutes($routePath = null)
    {
        $routePath = $routePath ?: dirname($_SERVER['DOCUMENT_ROOT']) . '/routes/web.php';

        $files = glob($routePath);

        foreach ($files as $file) {
            require $file;
        }
    }

    /**
     * Load error and exception handler
     * @return void
     */
    public function loadErrorAndExceptionHandler()
    {
        error_reporting(E_ALL);
        set_error_handler('Awesome\Error::errorHandler');
        set_exception_handler('Awesome\Error::exceptionHandler');
    }

    /**
     * Runs the application
     * @return void
     */
    public static function initializeRouter()
    {
        self::$router = self::$container->get(Router::class);
        self::$router->dispatch(str_replace('url=', '', $_SERVER['QUERY_STRING']));
    }

   /**
     * Load repositories
     */
    public static function loadRepositories(
        $contractsPath = null,
        $contractsNamespace = 'App\Contracts\\',
        $repositoriesNamespace = 'App\Repositories\\',
        $contractsSufix = 'Contract',
        $repositoriesSufix = 'Repository'
    ) {
        $contractsPath = $contractsPath ?: dirname($_SERVER['DOCUMENT_ROOT']) . '/App/Contracts/*.php';

        // get all contracts files
        $files = glob($contractsPath);

        // bind each contract to its repository
        foreach ($files as $file) {
            $class = basename($file, '.php');
            $contract = $contractsNamespace . $class;
            $repository = $repositoriesNamespace . str_replace($contractsSufix, $repositoriesSufix, $class);
            
            try {
                $repositoryClass = self::$container->get($repository);
                self::$container->set($contract, $repositoryClass);
            } catch (\Exception $th) {
                throw new Exception('Error loading repository for ' . $class);
            }
        }
    }

    /**
     * Runs the application
     * @return void
     */
    public function run()
    {
        self::loadErrorAndExceptionHandler();
        self::loadRoutes();
        self::initializeRouter();
    }
}
