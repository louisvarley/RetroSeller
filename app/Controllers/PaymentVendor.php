<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use \Core\Services\EntityService as Entities;

/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class PaymentVendor extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Payment Vendor", "description" => "Vendors who provide Payment Services"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$vendor = Entities::findEntity($this->route_params['controller'], $id);
		$vendor->setName($data[$this->route_params['controller']]['name']);
		$vendor->setFixedFee($data[$this->route_params['controller']]['fixed_fee']);
		$vendor->setPercentageFee($data[$this->route_params['controller']]['percentage_fee']);
		$vendor->setColor($data[$this->route_params['controller']]['color']);
		
		Entities::persist($vendor);
		Entities::flush();
		
	}
	
	public function insertEntity($data){

		$vendor = new \App\Models\PaymentVendor();

		$vendor->setName($data[$this->route_params['controller']]['name']);
		$vendor->setFixedFee($data[$this->route_params['controller']]['fixed_fee']);
		$vendor->setPercentageFee($data[$this->route_params['controller']]['percentage_fee']);
		$vendor->setColor($data[$this->route_params['controller']]['color']);
		
		Entities::persist($vendor);
		Entities::flush();

		return $vendor->getId();
		
	}		
	
}
