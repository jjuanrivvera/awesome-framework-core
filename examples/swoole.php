<?php
/**
 * Server file
 * PHP version 8.2
 * @author Juan Felipe Rivera G
 */

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__));
$dotenv->safeLoad();

$app = new Awesome\App(
    configPath: dirname(__FILE__) . '/config/*.php',
    routesPath: dirname(__FILE__) . '/routes/*.php',
    viewPath: './App/Views',
    isCli: true
);
$app->loadRepositories('./App/Contracts/*.php');
$app->init();

$server = new Swoole\Http\Server('0.0.0.0', 9000);

$server->on("start", function () {
    echo "Swoole http server is started at http://0.0.0.0:9000\n";
});

$server->on("request", function ($request, $response) use ($app) {
    container()->set('Awesome\Http\Request', new App\Http\Request(
        method: $request->server["request_method"],
        headers: $request->header,
        body: $request->getContent(),
        uri: $request->server["path_info"]
    ));

    $response->end($app->run());
});

$server->start();
