<?php

namespace Awesome;

use Exception;
use Psr\Http\Message\RequestInterface;
use Awesome\Interfaces\ConfigInterface;
use Awesome\Interfaces\ContainerInterface;

/**
 * Class App
 * @package Awesome
 * @author  Juan Felipe Rivera G
 */
class App
{
    /**
     * Application instance
     * @var App
     */
    private static ?App $instance = null;

    /**
     * Container instance
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var Router|null
     */
    private ?Router $router = null;

    /**
     * @var bool|null
     */
    private ?bool $isCli = null;

    /**
     * @var ConfigInterface
     */
    private ?ConfigInterface $config = null;

    /**
     * @var string
     */
    private string $routesPath;

    /**
     * @var string
     */
    private string $viewPath;

    /**
     * App Constructor
     * @param ConfigInterface|null $config
     * @param string|null $routesPath
     * @param string|null $viewPath
     * @param bool $isCli
     * @throws Exception
     */
    private function __construct(
        ConfigInterface $config = null,
        string $routesPath = null,
        string $viewPath = null,
        bool $isCli = null
    ) {
        $this->isCli = $isCli ?? php_sapi_name() === 'cli';
        $this->config = $config ?? new Config();
        $this->routesPath = $routesPath ?? dirname(__DIR__) . '/routes/*.php';
        $this->viewPath = $viewPath ?? '../App/Views';

        $this->initializeContainer();
    }

    /**
     * Prevents cloning of the instance.
     */
    private function __clone()
    {
    }

    /**
     * Prevents unserialization of the instance.
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize singleton');
    }

    /**
     * Get application instance
     * @param ConfigInterface|null $config
     * @param string|null $routesPath
     * @param string|null $viewPath
     * @param bool|null $isCli
     * @return self
     * @throws Exception
     */
    public static function getInstance(
        ConfigInterface $config = null,
        string $routesPath = null,
        string $viewPath = null,
        bool $isCli = null
    ): self {
        if (is_null(self::$instance)) {
            self::$instance = new self($config, $routesPath, $viewPath, $isCli);
        }

        return self::$instance;
    }

    /**
     * Initialize container
     * @return void
     * @throws Exception
     */
    public function initializeContainer(): void
    {
        $this->container = new Container();
    }

    /**
     * Get container instance
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Get router instance
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Get config instance
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * Add router instance
     * @param string $class
     * @return void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function addRouter(string $class): void
    {
        $this->router = $this->container->get($class);
    }

    /**
     * Know if the application is running in cli
     * @return bool
     */
    public function isCli(): bool
    {
        return $this->isCli;
    }

    /**
     * Get route path
     * @return string
     */
    public function getRoutesPath(): string
    {
        return $this->routesPath;
    }

    /**
     * Get view path
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * Load routes by requiring all files in the routes directory
     * @return void
     */
    public function loadRoutes(): void
    {
        if (!is_dir(dirname($this->routesPath))) {
            throw new Exception('Routes directory not found');
        }

        $files = glob($this->routesPath);

        foreach ($files as $file) {
            require $file;
        }
    }

    /**
     * Load error and exception handlers
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
    public function loadRouter(): void
    {
        $this->router = $this->router ?: $this->container->get(Router::class);
    }

    /**
     * Load repositories
     * @param string|null $contractsPath
     * @param string $contractsNamespace
     * @param string $repositoriesNamespace Namespace of repositories
     * @param string $contractsSuffix
     * @param string $repositoriesSuffix
     * @return void
     * @throws Exception
     */
    public function loadRepositories(
        string $contractsPath = null,
        string $contractsNamespace = 'App\Contracts\\',
        string $repositoriesNamespace = 'App\Repositories\\',
        string $contractsSuffix = 'Contract',
        string $repositoriesSuffix = 'Repository'
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
                $repositoryClass = $this->container->get($repository);
                $this->container->set($contract, $repositoryClass);
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
    public function init(): void
    {
        $this->loadErrorAndExceptionHandler();
        $this->loadRoutes();
        $this->loadRouter();
    }

    /**
     * Run application
     * @return mixed
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws Exception
     */
    public function run(RequestInterface $request = null): mixed
    {
        return $this->router->dispatch($request);
    }
}
