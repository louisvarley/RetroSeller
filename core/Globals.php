<?php

use Core\EntityManager;
use Core\SessionManager;
use Core\ToastManager;
use Core\ActionsManager;

/**
 * Config
 */
 
/* Directories */
define("DIR_ROOT", dirname(dirname(__FILE__)));
define("DIR_APP", DIR_ROOT . '/app');	
define("DIR_CORE", DIR_ROOT . '/core');	
define("DIR_PUBLIC", DIR_ROOT . '/public');
define("DIR_STATIC", DIR_PUBLIC  . '/static');	
define("DIR_PROXIES", DIR_CORE  . '/proxies');

define("WWW_STATIC", '/static');	
define("WWW_JS", WWW_STATIC  . '/js');		
define("WWW_CSS", WWW_STATIC  . '/css');	

/* Name Spaces */
define("_MODELS", "\\App\\Models\\");
define("_CONTROLLERS", "\\App\\Controllers\\");
define("_VIEWS", "\\App\\Views\\");	


/* Statuses */
define("_PURCHASE_STATUSES", array(
	'FOR_SALE' => array('id' => 1, 'name' => 'For Sale'),
	'SOLD' => array('id' => 2, 'name' => 'Sold'),
	'BOUGHT_OUT' => array('id' => 3, 'name' => 'Bought Out'),
	'WRITTEN_OFF' => array('id' => 4, 'name' => 'Written Off'),
	'REQUIRES_REPAIR' => array('id' => 5, 'name' => 'Requires Repair'),
	'HELD' => array('id' => 6, 'name' => 'Held'),
	)
);

/* CLI Mode */
if(php_sapi_name() !== 'cli'){
	define("_URL", ( empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

define('_SHOW_ERRORS', true); 
 
if(file_exists(dirname(dirname(__FILE__)) . "/app/Config.php")){
	require(dirname(dirname(__FILE__)) . "/app/Config.php");
}

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * First Launch
 */
if(!defined("_FIRST_LAUNCH")){

	if($_SERVER['REQUEST_URI'] != "/setup"){
		header('Location: /setup');
	}
	return false;
}

/**
* Global Functions
*/

function toastManager(){	
	return \Core\ToastManager::instance();
}

function sessionManager(){	
	return \Core\SessionManager::instance();
}

function authenticationManager(){	
	return \Core\AuthenticationManager::instance();
}

function jobManager(){	
	return \Core\JobManager::instance();
}

function entityManager(){	
	return \Core\EntityManager::instance()->entityManager;
}

function findEntity($model, $id){
	$model = ucfirst($model);	
	return \Core\EntityManager::instance()->entityManager->find(_MODELS . $model, $id);	
}

function findBy($model, $criteria, $orderBy = null, $limit = null, $offset = null){
	$model = ucfirst($model);
	return \Core\EntityManager::instance()->entityManager->getRepository(_MODELS . $model)->findBy($criteria, $orderBy, $limit, $offset);	
}

function findByNot($model, $criteria, $orderBy = null, $limit = null, $offset = null){
	$model = ucfirst($model);	
	return \Core\EntityManager::instance()->findByNot(_MODELS . $model, $criteria, $orderBy, $limit, $offset);	
}

function findAll($model){
	$model = ucfirst($model);	
	return \Core\EntityManager::instance()->entityManager->getRepository(_MODELS . $model)->findAll();	
}

function createQuery($query){	
	return entityManager()->createQuery($query);
}

function createQueryBuilder($fields = null){	
	return entityManager()->createQueryBuilder($fields);
}

function createOptionSet($model, $valueField, $textField){
	
	$qb = entityManager()->createQueryBuilder($model);
	$qb->from(_MODELS . $model, "u");
	$qb->addSelect("u" . '.' . $valueField . ' AS value');


	if(!is_array($textField)){
		$qb->addSelect("u" . '.' . $textField . ' AS text');
		$qb->orderBy("u" . '.' . $textField, 'ASC');
	}else{
		
		/* handles turning multiple columns into a JSON array for select2 to display */
		$c = "";
		foreach($textField as $field){		
			$c = $c. "'\"" . $field . "\":\"',". "u" . '.' . $field . "," . "'\",',";	
		}

		$c = rtrim($c,",'") . "'";
		$c = "CONCAT('{'," . $c . ",'}') as text";	
	
		$qb->addSelect($c);
		$qb->orderBy("u" . '.' . $textField[0], 'ASC');
		
	}
	
	
	$query = $qb->getQuery();
	
	$res = $query->getResult();
	
	return $res;
}


if(file_exists(dirname(dirname(__FILE__)) . "/app/actions.php")){
	require(dirname(dirname(__FILE__)) . "/app/actions.php");
}

/**
 * Calls
 */
sessionManager()->start();
