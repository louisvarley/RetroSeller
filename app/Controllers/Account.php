<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use \Core\Services\entityService as Entities;

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
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id),
			'countries' => \Core\Classes\Countries::fetch(),
		);	
	} 

	public function updateEntity($id, $data){
		
		$account = Entities::findEntity($this->route_params['controller'], $id);
		$account->setName($data['account']['name']);
		$account->setColor($data[$this->route_params['controller']]['color']);		

		$account->setPayPalEmailAddress($data['account']['paypal_email_address']);
		$account->setAccountNumber($data['account']['account_number']);
		$account->setAccountSortCode($data['account']['account_sort_code']);
		$account->setBusinessName($data['account']['business_name']);
		

		if($data['account']['address']){
			
				if($account->getBusinessAddress() == null){
					$account->setBusinessAddress(new \app\Models\Address());
					Entities::persist($account->getBusinessAddress());
				}
				$account->getBusinessAddress()->setLine1($data['account']['address']['line1']);	
				$account->getBusinessAddress()->setLine2($data['account']['address']['line2']);	
				$account->getBusinessAddress()->setCity($data['account']['address']['city']);	
				$account->getBusinessAddress()->setState($data['account']['address']['state']);	
				$account->getBusinessAddress()->setPostalCode($data['account']['address']['postalcode']);
				$account->getBusinessAddress()->setCountry($data['account']['address']['country']);				
		}

		Entities::persist($account);
		Entities::flush();
		
	}
	
	public function insertEntity($data){

		$account = new \App\Models\Account();
		$account->setName($data[$this->route_params['controller']]['name']);
		$account->setColor($data[$this->route_params['controller']]['color']);		
		
		$account->setPayPalEmailAddress($data['account']['paypal_email_address']);
		$account->setAccountNumber($data['account']['account_number']);
		$account->setAccountSortCode($data['account']['account_sort_code']);
		$account->setBusinessName($data['account']['business_name']);
				
		if($data['account']['address']){
			
				if($account->getBusinessAddress() == null){
					$account->setBusinessAddress(new \app\Models\Address());
					Entities::persist($account->getBusinessAddress());
				}
				$account->getBusinessAddress()->setLine1($data['account']['address']['line1']);	
				$account->getBusinessAddress()->setLine2($data['account']['address']['line2']);	
				$account->getBusinessAddress()->setCity($data['account']['address']['city']);	
				$account->getBusinessAddress()->setState($data['account']['address']['state']);	
				$account->getBusinessAddress()->setPostalCode($data['account']['address']['postalcode']);
				$account->getBusinessAddress()->setCountry($data['account']['address']['country']);					
		}				
				
		Entities::persist($account);
		Entities::flush();

		return $account->getId();
		
	}	
	
}
