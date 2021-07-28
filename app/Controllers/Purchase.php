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
 

class Purchase extends \App\Controllers\ManagerController
{
	
	
	public $page_data = ["title" => "Purchases", "description" => "Purchases are one item purchased"];		

	public function getEntity($id = 0){
		
		
		return array(
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id),
			"purchaseVendors" => Entities::createOptionSet('PurchaseVendor', 'id','name'),
			"purchaseStatuses" => Entities::createOptionSet('PurchaseStatus', 'id','name'),
			"purchaseCategories" => Entities::createOptionSet('purchaseCategory', 'id', 'path'),
			"accounts" => Entities::createOptionSet('Account', 'id','name'),				
		);	
	} 

	public function updateEntity($id, $data){
		
		$purchase = Entities::findEntity($this->route_params['controller'], $id);
		$purchaseVendor = Entities::findEntity("PurchaseVendor", $data['purchase']['purchase_vendor_id']);
		$purchaseStatus = Entities::findEntity("PurchaseStatus", $data['purchase']['status']);
		$purchaseCategory = Entities::findEntity("PurchaseCategory", $data['purchase']['category']);
		
		$purchase->setName($data['purchase']['name']);
		$purchase->setDescription($data['purchase']['description']);
		$purchase->setPurchaseVendor($purchaseVendor);
		$purchase->setDate(date_create_from_format('d/m/Y', $data['purchase']['date']));
		
		if($purchase->getBuyOut() != null){
			$purchase->setStatus(Entities::findEntity("PurchaseStatus", _PURCHASE_STATUSES['BOUGHT_OUT']['id']));					
		}else{
			$purchase->setStatus($purchaseStatus);			
		}

		$purchase->setValuation($data['purchase']['valuation']);
		$purchase->setCategory($purchaseCategory);		
		
		Entities::persist($purchase);


		if(isset($data['note']) &&  $data['note'] != ""){

			$note = new \App\Models\PurchaseNote();
			$note->setPurchase($purchase);
			$note->setNote($data['note']);
			$note->setDate(new \DateTime('now'));
			$note->setUser();
			Entities::persist($note);

		}

		Entities::flush();

	}
	
	public function insertEntity($data){
		
		$purchaseVendor = Entities::findEntity("PurchaseVendor", $data['purchase']['purchase_vendor_id']);
		$purchaseStatus = Entities::findEntity("PurchaseStatus", $data['purchase']['status']);
		$purchaseCategory = Entities::findEntity("PurchaseCategory", $data['purchase']['category']);
		
		$purchase = new \App\Models\Purchase();
		$purchase->setName($data['purchase']['name']);
		$purchase->setDescription($data['purchase']['description']);
		$purchase->setPurchaseVendor($purchaseVendor);
		$purchase->setStatus($purchaseStatus);
		$purchase->setDate(date_create_from_format('d/m/Y', $data['purchase']['date']));	
		$purchase->setValuation($data['purchase']['valuation']);
		$purchase->setCategory($purchaseCategory);	
			
		Entities::persist($purchase);	
			
		if(isset($data['purchase']['account_id']) && !empty($data['purchase']['account_id']) && !empty($data['purchase']['amount'])){
			
			$account = Entities::findEntity("Account", $data['purchase']['account_id']);
			
			$expense = new \App\Models\Expense();
			$expense->setName($data['purchase']['name']);
			$expense->setAmount($data['purchase']['amount']);
			$expense->setDate(date_create_from_format('d/m/Y', $data['purchase']['date']));		
			$expense->setAccount($account);
			$expense->getPurchases()->add($purchase);
			Entities::persist($expense);
			
		}	
		
		Entities::flush();
		
		return $purchase->getId();
		
	}	
	
}
