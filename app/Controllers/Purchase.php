<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Purchase extends \App\Controllers\ManagerController
{
	
	
	public $page_data = ["title" => "Purchases", "description" => "Purchases are one item purchased"];		

	public function getEntity($id = 0){
		
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id),
			"purchaseVendors" => createOptionSet('PurchaseVendor', 'id','name'),
			"purchaseStatuses" => createOptionSet('PurchaseStatus', 'id','name'),
			"purchaseCategories" => createOptionSet('purchaseCategory', 'id', 'path'),		
		);	
	} 

	public function updateEntity($id, $data){
		
		$purchase = findEntity($this->route_params['controller'], $id);
		$purchaseVendor = findEntity("PurchaseVendor", $data['purchase']['purchase_vendor_id']);
		$purchaseStatus = findEntity("PurchaseStatus", $data['purchase']['status']);
		$purchaseCategory = findEntity("PurchaseCategory", $data['purchase']['category']);
		
		$purchase->setName($data['purchase']['name']);
		$purchase->setDescription($data['purchase']['description']);
		$purchase->setPurchaseVendor($purchaseVendor);
		$purchase->setDate(date_create_from_format('d/m/Y', $data['purchase']['date']));	
		
		if($purchase->getBuyOut() != null){
			$purchase->setStatus(findEntity("PurchaseStatus", _PURCHASE_STATUSES['BOUGHT_OUT']['id']));					
		}else{
			$purchase->setStatus($purchaseStatus);			
		}

		$purchase->setValuation($data['purchase']['valuation']);
		$purchase->setCategory($purchaseCategory);		
		
		entityManager()->persist($purchase);
		entityManager()->flush();
		
	}
	
	public function insertEntity($data){
		
		$purchaseVendor = findEntity("PurchaseVendor", $data['purchase']['purchase_vendor_id']);
		$purchaseStatus = findEntity("PurchaseStatus", $data['purchase']['status']);
		$purchaseCategory = findEntity("PurchaseCategory", $data['purchase']['category']);
		
		$purchase = new \App\Models\Purchase();
		$purchase->setName($data['purchase']['name']);
		$purchase->setDescription($data['purchase']['description']);
		$purchase->setPurchaseVendor($purchaseVendor);
		$purchase->setStatus($purchaseStatus);
		$purchase->setDate(date_create_from_format('d/m/Y', $data['purchase']['date']));	
		$purchase->setValuation($data['purchase']['valuation']);
		$purchase->setCategory($purchaseCategory);	
			
		entityManager()->persist($purchase);
		entityManager()->flush();
		
		return $purchase->getId();
		
	}	
	
}