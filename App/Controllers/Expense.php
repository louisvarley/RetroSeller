<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Expense extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Expense", "description" => "Expenses are a spend which is tied to one or more purchases"];	

	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id),
			"purchases" => createOptionSet('purchase', 'id',['id','name','date']),	
			"accounts" => createOptionSet('account', 'id','name'),				
		);	
	} 

	public function updateEntity($id, $data){
		
		$expense = findEntity($this->route_params['controller'], $id);
		$account = findEntity("Account", $data['expense']['account_id']);
		
		$expense->setName($data['expense']['name']);
		$expense->setAmount($data['expense']['amount']);
		$expense->setDate(date_create_from_format('d/m/Y', $data['expense']['date']));			
		$expense->setAccount($account);
		$expense->getPurchases()->clear();	
		

		foreach($data['expense']['purchases'] as $purchase_id){
			$expense->getPurchases()->add(findEntity("Purchase", $purchase_id));
		}

		entityManager()->persist($expense);
		entityManager()->flush();
		
	}
	
	public function insertEntity($data){

		$expense = new \App\Models\Expense();

		$account = findEntity("Account", $data['expense']['account_id']);
		
		$expense->setName($data['expense']['name']);
		$expense->setAmount($data['expense']['amount']);
		$expense->setDate(date_create_from_format('d/m/Y', $data['expense']['date']));		
		$expense->setAccount($account);
		
		foreach($data['expense']['purchases'] as $purchase_id){
			$expense->getPurchases()->add(findEntity("Purchase", $purchase_id));
		}
		
		entityManager()->persist($expense);
		entityManager()->flush();

		return $expense->getId();
		
	}	
	
}
