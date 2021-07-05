<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class SaleVendor extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Sale Vendor", "description" => "Vendors who provide Sale Services"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$vendor = findEntity($this->route_params['controller'], $id);
		$vendor->setName($data[$this->route_params['controller']]['name']);
		$vendor->setFixedFee($data[$this->route_params['controller']]['fixed_fee']);
		$vendor->setPercentageFee($data[$this->route_params['controller']]['percentage_fee']);
		
		EntityService()->persist($vendor);
		EntityService()->flush();
		
	}
	
	public function insertEntity($data){

		$vendor = new \App\Models\SaleVendor();

		$vendor->setName($data[$this->route_params['controller']]['name']);
		$vendor->setFixedFee($data[$this->route_params['controller']]['fixed_fee']);
		$vendor->setPercentageFee($data[$this->route_params['controller']]['percentage_fee']);	
		
		EntityService()->persist($vendor);
		EntityService()->flush();

		return $vendor->getId();
		
	}		
	
}
