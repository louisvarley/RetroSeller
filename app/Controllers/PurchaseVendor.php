<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class PurchaseVendor extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Purchase Vendor", "description" => "Vendors who provide Purchase Services"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$vendor = findEntity($this->route_params['controller'], $id);
		$vendor->setName($data[$this->route_params['controller']]['name']);

		EntityService()->persist($vendor);
		EntityService()->flush();
		
	}
	
	public function insertEntity($data){

		$vendor = new \App\Models\PurchaseVendor();

		$vendor->setName($data[$this->route_params['controller']]['name']);
		
		EntityService()->persist($vendor);
		EntityService()->flush();

		return $vendor->getId();
		
	}	
	
}
