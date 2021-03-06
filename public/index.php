<?php
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Application;
use Dotenv\Dotenv;

// Load environment variables
if (class_exists(Dotenv::class)) {
    $dotenv = new Dotenv(__DIR__ . '/..');
    $dotenv->load();
    $dotenv->required('APP_ENV')->allowedValues(['pro', 'dev']);
}

// Set error reporting
if (getenv('APP_ENV') === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

/** @var ContainerInterface $container */
$container = include __DIR__ . '/../config/container.php';
/** @var Application $app */
$app = $container->get(Application::class);
$app->run($container->get(ServerRequestInterface::class));
