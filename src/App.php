<?php

namespace Awesome;

use Exception;
use DI\Container;
use DI\ContainerBuilder;

/**
 * Class App
 * @package Awesome
 * @author  Juan Felipe Rivera G
 */
class App
{
    /**
     * Default application environment
     * @var string
     */
    private const DEFAULT_APP_ENVIRONMENT = 'production';

    /**
     * Container instance
     * @var Container
     */
    protected static $container;

    /**
     * @var Router
     */
    protected static $router;

    /**
     * @var bool
     */
    protected static $isCli;

    /**
     * @var string
     */
    protected static $configPath;

    /**
     * @var string
     */
    protected static $routesPath;

    /**
     * @var string
     */
    protected static $viewPath;

    /**
     * App Constructor
     * @param string|null $configPath
     * @param string|null $routesPath
     * @param string|null $viewPath
     * @param bool|null $isCli
     * @throws Exception
     */
    public function __construct(
        string $configPath = null,
        string $routesPath = null,
        string $viewPath = null,
        bool $isCli = false
    ) {
        self::$isCli = $isCli ?? php_sapi_name() === 'cli';
        self::$configPath = $configPath ?? dirname(__DIR__) . '/config/*.php';
        self::$routesPath = $routesPath ?? dirname(__DIR__) . '/routes/*.php';
        self::$viewPath = $viewPath ?? '../App/Views';

        $this->initializeContainer();
    }

    /**
     * Initialize container
     * @return void
     * @throws Exception
     */
    public function initializeContainer()
    {
        $container = new Container();

        $env = isset($_ENV['APP_ENV']) ? $_ENV['APP_ENV'] : self::DEFAULT_APP_ENVIRONMENT;

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
    public static function getContainer(): Container
    {
        return self::$container;
    }

    /**
     * Get router instance
     * @return Router
     */
    public static function getRouter(): Router
    {
        return self::$router;
    }

    /**
     * Add router instance
     * @param $class
     * @return void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function addRouter($class): void
    {
        self::$router = self::$container->get($class);
    }

    /**
     * Know if the application is running in cli
     * @return bool
     */
    public static function isCli(): bool
    {
        return self::$isCli;
    }

    /**
     * Get config path
     */
    public static function getConfigPath(): string
    {
        return self::$configPath;
    }

    /**
     * Get route path
     */
    public static function getRoutesPath(): string
    {
        return self::$routesPath;
    }

    /**
     * Get view path
     */
    public static function getViewPath(): string
    {
        return self::$viewPath;
    }

    /**
     * Load routes
     * @return void
     */
    public static function loadRoutes(): void
    {
        $files = glob(self::$routesPath);

        foreach ($files as $file) {
            require $file;
        }
    }

    /**
     * Load error and exception handler
     * @return void
     */
    public function loadErrorAndExceptionHandler(): void
    {
        error_reporting(E_ALL);
        set_error_handler('Awesome\Error::errorHandler');
        set_exception_handler('Awesome\Error::exceptionHandler');
    }

    /**
     * Runs the application
     * @return void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws Exception
     */
    public static function loadRouter()
    {
        self::$router = self::$router ?: self::$container->get(Router::class);
    }

    /**
     * Load repositories
     * @param null $contractsPath
     * @param string $contractsNamespace
     * @param string $repositoriesNamespace Namespace of repositories
     * @param string $contractsSuffix
     * @param string $repositoriesSuffix
     * @return void
     * @throws Exception
     */
    public static function loadRepositories(
        $contractsPath = null,
        $contractsNamespace = 'App\Contracts\\',
        $repositoriesNamespace = 'App\Repositories\\',
        $contractsSuffix = 'Contract',
        $repositoriesSuffix = 'Repository'
    ) {
        $contractsPath = $contractsPath ?: dirname($_SERVER['DOCUMENT_ROOT']) . '/App/Contracts/*.php';

        // get all contracts files
        $files = glob($contractsPath);

        // bind each contract to its repository
        foreach ($files as $file) {
            $class = basename($file, '.php');
            $contract = $contractsNamespace . $class;
            $repository = $repositoriesNamespace . str_replace($contractsSuffix, $repositoriesSuffix, $class);

            try {
                $repositoryClass = self::$container->get($repository);
                self::$container->set($contract, $repositoryClass);
            } catch (\Exception $th) {
                throw new Exception('Error loading repository for ' . $class);
            }
        }
    }

    /**
     * Init application
     * @return void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function init()
    {
        self::loadErrorAndExceptionHandler();
        self::loadRoutes();
        self::loadRouter();
    }

    /**
     * Run application
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function run(Request $request = null)
    {
        return self::$router->dispatch($request);
    }
}
