<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class eBayIntergration extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "eBay Intergrations", "description" => "Users who can access RetroSeller"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$eBayIntergration = findEntity($this->route_params['controller'], $id);

		$eBayIntergration->setUserId($data['eBayIntergration']['userId']);
		$eBayIntergration->setDevId($data['eBayIntergration']['devId']);
		$eBayIntergration->setAppId($data['eBayIntergration']['appId']);
		$eBayIntergration->setCertId($data['eBayIntergration']['certId']);
		$eBayIntergration->setAuthToken($data['eBayIntergration']['authToken']);
		
		entityManager()->persist($eBayIntergration);
		entityManager()->flush();
		
	}
	
	public function insertEntity($data){

		$eBayIntergration = new \App\Models\eBayIntergration();

		$eBayIntergration->setUserId($data['eBayIntergration']['userId']);
		$eBayIntergration->setDevId($data['eBayIntergration']['devId']);
		$eBayIntergration->setAppId($data['eBayIntergration']['appId']);
		$eBayIntergration->setCertId($data['eBayIntergration']['certId']);
		$eBayIntergration->setAuthToken($data['eBayIntergration']['authToken']);		
		
		entityManager()->persist($eBayIntergration);
		entityManager()->flush();

		return $eBayIntergration->getId();
		
	}	
	
}
