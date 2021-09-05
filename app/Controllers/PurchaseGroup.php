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
 

class PurchaseGroup extends \App\Controllers\ManagerController
{
	
	
	public $page_data = ["title" => "Purchase Groups", "description" => "Purchase groups allow you to group purchases together for reporting purposes"];		

	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id),			
			"purchases" => ($id > 0 ? Entities::createOptionSet('purchase', 'id',['id','name','date']) : Entities::createOptionSet('purchase', 'id',['id','name','date'])),			
		);	
	} 

	public function updateEntity($id, $data){
		
		$purchaseGroup = Entities::findEntity($this->route_params['controller'], $id);

		$purchaseGroup->setName($data['purchaseGroup']['name']);
		$purchaseGroup->setDescription($data['purchaseGroup']['description']);
	
		$purchases = Entities::findBy("Purchase", ['purchaseGroup' => $purchaseGroup]);
			
		
		foreach($purchases as $purchase){
			$purchase->setPurchaseGroup(null);
			Entities::persist($purchase);
		}		
		
		foreach($data['purchaseGroup']['purchases'] as $purchase_id){
			$purchase = Entities::findEntity("purchase", $purchase_id);
			$purchase->setPurchaseGroup($purchaseGroup);
			Entities::persist($purchase);
		}		
		
		Entities::persist($purchaseGroup);
		Entities::flush();

	}
	
	public function insertEntity($data){

		$purchaseGroup = new \App\Models\PurchaseGroup();
		$purchaseGroup->setName($data['purchaseGroup']['name']);
		$purchaseGroup->setDescription($data['purchaseGroup']['description']);
		
		Entities::persist($purchaseGroup);	

		foreach($data['purchaseGroup']['purchases'] as $purchase_id){
			$purchase = Entities::findEntity("purchase", $purchase_id);
			$purchase->setPurchaseGroup($purchaseGroup);
			Entities::persist($purchase);
		}	
		
		Entities::flush();
		
		return $purchaseGroup->getId();
		
	}	
	
}
