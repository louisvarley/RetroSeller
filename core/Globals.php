<?php

use Core\EntityManager;
use Core\SessionManager;
use Core\ToastManager;
use Core\Services\eBayService;

use Doctrine\ORM\Query\ResultSetMapping;


/**
 * Config
 */
 
/* Directories */
define("DIR_ROOT", dirname(dirname(__FILE__)));
define("DIR_APP", DIR_ROOT . '/app');	
define("DIR_CORE", DIR_ROOT . '/core');	
define("DIR_PUBLIC", DIR_ROOT . '/public');
define("DIR_STATIC", DIR_PUBLIC  . '/static');	
define("DIR_PROXIES", DIR_CORE  . '/Proxies');

define("DIR_VIEWS", DIR_APP . '/Views');	
define("DIR_CONTROLLERS", DIR_APP . '/Controllers');	
define("DIR_MODELS", DIR_APP . '/Models');	

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

/* Find a Single Entity by ID */
function findEntity($model, $id){
	$model = ucfirst($model);	
	return \Core\EntityManager::instance()->entityManager->find(_MODELS . $model, $id);	
}

/* Find Multiple Entities By a Matching Criteria */
function findBy($model, $criteria, $orderBy = null, $limit = null, $offset = null){
	$model = ucfirst($model);
	return \Core\EntityManager::instance()->entityManager->getRepository(_MODELS . $model)->findBy($criteria, $orderBy, $limit, $offset);	
}

/* Find Multiple Entities By a Not Matching Criteria */
function findByNot($model, $criteria, $orderBy = null, $limit = null, $offset = null){
	$model = ucfirst($model);	
	return \Core\EntityManager::instance()->findByNot(_MODELS . $model, $criteria, $orderBy, $limit, $offset);	
}

/* Find All Entities */
function findAll($model){
	$model = ucfirst($model);	
	return entityManager()->getRepository(_MODELS . $model)->findAll();	
}

/* Create a Query from Scratch */
function createQuery($query){	
	return entityManager()->createQuery($query);
}

/* Create Query Builder From Scratch */
function createQueryBuilder($fields = null){	
	return entityManager()->createQueryBuilder($fields);
}

/* Create a Named Query */
function createdNamedQuery($model, $namedQuery){
	return entityManager()->getRepository(_MODELS . $model)->createNamedQuery($namedQuery)->getResult();	
}

/* Create an Optionset */
function createOptionSet($model, $valueField, $textField, $criteria = null){
	
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
	
	if($criteria != null){
	
		foreach($criteria as $key => $value){
			$qb->Where('u.' . $key . ' ' . $value['comparison'] . ' :value');
			$qb->setParameter('value', $value['match']);
			
		}

	}
		
	$query = $qb->getQuery();
	
	$res = $query->getResult();
	
	return $res;
}

function getMetadata($key){

	$meta = findBy("metadata",["key" => $key]);

	if($meta){
		return $meta[0]->getValue();
	}else{
		return null;
	}

}

function setMetadata($key, $value){

	if(findBy("metadata",["key" => $key])){

		$meta = findBy("metadata",["key" => $key])[0];
		$meta->setValue($value);
	

	}else{

		$meta = new \App\Models\Metadata();
		$meta->setValue($value);
		$meta->setKey($key);


	}

	entityManager()->persist($meta);
	entityManager()->flush();


}

function eBayService(){	
	return \Core\Services\ebayService::instance();
}

sessionManager()->start();


