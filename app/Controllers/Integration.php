<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class integration extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "eBay integration", "description" => "eBay integration Settings"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$integration = findEntity($this->route_params['controller'], $id);

		$integration->setUserId($data['integration']['userId']);
		$integration->setDevId($data['integration']['devId']);
		$integration->setAppId($data['integration']['appId']);
		$integration->setCertId($data['integration']['certId']);
		$integration->setAuthToken($data['integration']['authToken']);
		
		entityManager()->persist($integration);
		entityManager()->flush();
		
	}
	
	public function insertEntity($data){

		$integration = new \App\Models\Integration();

		$integration->setUserId($data['integration']['userId']);
		$integration->setDevId($data['integration']['devId']);
		$integration->setAppId($data['integration']['appId']);
		$integration->setCertId($data['integration']['certId']);
		$integration->setAuthToken($data['integration']['authToken']);		
		
		entityManager()->persist($integration);
		entityManager()->flush();

		return $integration->getId();
		
	}	
	
}
