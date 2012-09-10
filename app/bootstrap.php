<?php

// Namespaces
use Nette\Diagnostics\Debugger,
    Nette\Application\Routers\Route;
use Nette\Forms;


// Load Nette Framework
$params['libsDir'] = __DIR__ . '/../libs';
require $params['libsDir'] . '/Nette/loader.php';
// Req dibi
require_once 'dibi/dibi.php';


// Load configuration and set configuration
$configurator = new Nette\Config\Configurator;
$configurator->addParameters($params);
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->addConfig(__DIR__ . '/config/config.neon');

// Nette Debugger
$configurator->enableDebugger(__DIR__ . '/../log');

//  robotloader
$robotLoader = $configurator->createRobotLoader()
        ->addDirectory('app')
        ->addDirectory('dibi')
        ->addDirectory('libs/Nette/Extras')
        ->setCacheStorage(new Nette\Caching\Storages\FileStorage('temp'))
        ->register();
$robotLoader->autoRebuild = TRUE;

// Sessions - manual start with expiration about 1 year for long-term logged users
$container = $configurator->createContainer();
$session = $container->session;
$container->session->setExpiration('+ 1 year');

if ($container->session->exists()) {
    $session = $container->session->start();
}

// Start dibi connection
dibi::connect($container->params['database']);

// Configure and run the application!
$application = $container->application;
$application->catchExceptions = TRUE;  
$application->errorPresenter = 'Error';

// Setup router
$container->router[] = new Route('index.php', 'Homepage:default', 
        Route::ONE_WAY);
$container->router[] = new Route('<presenter>/<action>[/<id>]', 
        'Homepage:default');

// DatePicker extension
Nette\Forms\Container::extensionMethod('addDatePicker',
        function (Nette\Forms\Container $container, $name, $label = NULL) {
    return $container[$name] = new DatePicker\DatePicker($label);
});

// Run the whole app
$application->run();


