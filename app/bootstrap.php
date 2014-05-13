<?php

// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';
require LIBS_DIR . '/htmlpurifier/library/HTMLPurifier.auto.php';


// Configure application
$configurator = new Nette\Config\Configurator;
// Enable Nette Debugger for error visualisation & logging
$configurator->setDebugMode(
	array(
		'89.177.54.0', '77.48.61.186', '94.112.214.90', '213.29.66.111',
		'88.100.222.68', '94.230.152.9', '83.208.83.64', '90.177.180.219'
	)
);

$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	 ->addDirectory(APP_DIR)
	 ->addDirectory(LIBS_DIR)
	 ->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config.neon');
$container = $configurator->createContainer();

/* Include route files */
require APP_DIR . '/routes.php';

// \Nette\Diagnostics\Debugger::addPanel(new IncludePanel);

// Configure and run the application!
$container->application->run();
