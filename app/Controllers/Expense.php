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
 

class Expense extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Expense", "description" => "Expenses are a spend which is tied to one or more purchases"];	

	public function getEntity($id = 0){

		$soldStatus = Entities::findEntity("PurchaseStatus", _PURCHASE_STATUSES['SOLD']['id']);
		
		return array(
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id),
			"purchases" => Entities::createOptionSet('purchase', 'id',['id','name','date']),				
			"accounts" => Entities::createOptionSet('Account', 'id','name'),				
		);	
	} 

	public function updateEntity($id, $data){
		
		$expense = Entities::findEntity($this->route_params['controller'], $id);
		$account = Entities::findEntity("Account", $data['expense']['account_id']);
		
		$expense->setName($data['expense']['name']);
		$expense->setAmount($data['expense']['amount']);
		$expense->setDate(date_create_from_format('d/m/Y', $data['expense']['date']));			
		$expense->setAccount($account);
		$expense->getPurchases()->clear();	
		

		foreach($data['expense']['purchases'] as $purchase_id){
			$expense->getPurchases()->add(Entities::findEntity("Purchase", $purchase_id));
		}

		Entities::persist($expense);
		Entities::flush();
		
	}
	
	public function insertEntity($data){

		$expense = new \App\Models\Expense();

		$account = Entities::findEntity("Account", $data['expense']['account_id']);
		
		$expense->setName($data['expense']['name']);
		$expense->setAmount($data['expense']['amount']);
		$expense->setDate(date_create_from_format('d/m/Y', $data['expense']['date']));		
		$expense->setAccount($account);
		
		foreach($data['expense']['purchases'] as $purchase_id){
			$expense->getPurchases()->add(Entities::findEntity("Purchase", $purchase_id));
		}
		
		Entities::persist($expense);
		Entities::flush();

		return $expense->getId();
		
	}	
	
}
