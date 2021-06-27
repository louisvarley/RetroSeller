<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Intergration extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "eBay Intergrations", "description" => "eBay Intergration Settings"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$Intergration = findEntity($this->route_params['controller'], $id);

		$Intergration->setUserId($data['intergration']['userId']);
		$Intergration->setDevId($data['intergration']['devId']);
		$Intergration->setAppId($data['intergration']['appId']);
		$Intergration->setCertId($data['intergration']['certId']);
		$Intergration->setAuthToken($data['intergration']['authToken']);
		
		entityManager()->persist($Intergration);
		entityManager()->flush();
		
	}
	
	public function insertEntity($data){

		$Intergration = new \App\Models\Intergration();

		$Intergration->setUserId($data['intergration']['userId']);
		$Intergration->setDevId($data['intergration']['devId']);
		$Intergration->setAppId($data['intergration']['appId']);
		$Intergration->setCertId($data['intergration']['certId']);
		$Intergration->setAuthToken($data['intergration']['authToken']);		
		
		entityManager()->persist($Intergration);
		entityManager()->flush();

		return $Intergration->getId();
		
	}	
	
}
