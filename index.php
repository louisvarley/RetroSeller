<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

use App\Config;

/**
 * Globals
 */
require 'core/Globals.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Core\Router();

/* ajax */
$router->add('api/{controller}/{action}', ['namespace' => 'Api']);

/* Controller Action With ID and File */
$router->add('{controller}/{id:\d+}.jpg', ['action' => 'index']);  

/* Controller Action With ID and File */
$router->add('{controller}/{size}/{id:\d+}.jpg', ['action' => 'index']);  

/* Fetching a Purchase first Blob Image */
$router->add('{controller}/purchase/{size}/{id:\d+}.jpg', ['action' => 'purchase']);  

/* Controller Plural 1 */
$router->add('{controller}s', ['controller' => '{controller}', 'action' => 'list']);

/* Controller Plural 2 */
$router->add('{controller}es', ['controller' => '{controller}', 'action' => 'list']);

/* Index Route */
$router->add('', ['controller' => 'Home', 'action' => 'index']);

/* Controller Index Route */
$router->add('{controller}', ['controller' => '{controller}', 'action' => 'index']);

/* Controller Action No ID */
$router->add('{controller}/{action}');

/* Controller Action With ID */
$router->add('{controller}/{action}/{id:\d+}');   


/* Controller Index With ID */
$router->add('{controller}/{id:\d+}', ['controller' => '{controller}', 'action' => 'index']);

/* Process the dispatch */	
$router->dispatch($_SERVER['QUERY_STRING']);
