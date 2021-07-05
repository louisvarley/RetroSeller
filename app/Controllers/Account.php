<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Account extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Account", "description" => "Accounts are virtual segregations or funds"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$account = findEntity($this->route_params['controller'], $id);
		$account->setName($data['account']['name']);
		$account->setColor($data[$this->route_params['controller']]['color']);		

		$account->setPayPalEmailAddress($data['account']['paypal_email_address']);
		$account->setAccountNumber($data['account']['account_number']);
		$account->setAccountSortCode($data['account']['account_sort_code']);
		$account->setBusinessName($data['account']['business_name']);
				
				
		
		EntityService()->persist($account);
		EntityService()->flush();
		
	}
	
	public function insertEntity($data){

		$account = new \App\Models\Account();
		$account->setName($data[$this->route_params['controller']]['name']);
		$account->setColor($data[$this->route_params['controller']]['color']);		
		
		$account->setPayPalEmailAddress($data['account']['paypal_email_address']);
		$account->setAccountNumber($data['account']['account_number']);
		$account->setAccountSortCode($data['account']['account_sort_code']);
		$account->setBusinessName($data['account']['business_name']);
				
		EntityService()->persist($account);
		EntityService()->flush();

		return $account->getId();
		
	}	
	
}
