<?php

use Core\EntityManager;
use Core\SessionManager;
use Core\ToastManager;
use Core\ActionsManager;
/**
 * Config
 */
require(dirname(dirname(__FILE__)) . "/app/config.php");

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


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
	return \Core\EntityManager::instance()->entityManager->find(_MODELS . $model, $id);	
}

function findBy($model, $criteria, $orderBy = null, $limit = null, $offset = null){
	return \Core\EntityManager::instance()->entityManager->getRepository(_MODELS . $model)->findBy($criteria, $orderBy, $limit, $offset);	
}

function findByNot($model, $criteria, $orderBy = null, $limit = null, $offset = null){
	return \Core\EntityManager::instance()->findByNot(_MODELS . $model, $criteria, $orderBy, $limit, $offset);	
}

function findAll($model){
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
