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
 

class Withdrawal extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Withdrawls", "description" => "When a balance is transfered to an account holder"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id)	,
			"accounts" => Entities::createOptionSet('Account', 'id','name'),				
		);	
	} 

	public function updateEntity($id, $data){
		
		$withdrawal = Entities::findEntity($this->route_params['controller'], $id);
		$account = Entities::findEntity("Account", $data['withdrawal']['account_id']);
		
		
		$withdrawal->setAccount($account);
		$withdrawal->setAmount($data['withdrawal']['amount']);
		$withdrawal->setDescription($data['withdrawal']['description']);
		$withdrawal->setDate(date_create_from_format('d/m/Y', $data['withdrawal']['date']));	

		Entities::persist($withdrawal);
		Entities::flush();
		
	}
	
	public function insertEntity($data){

		$withdrawal = new \App\Models\Withdrawal();

		$account = Entities::findEntity("Account", $data['withdrawal']['account_id']);
		
		
		$withdrawal->setAccount($account);
		$withdrawal->setAmount($data['withdrawal']['amount']);
		$withdrawal->setDescription($data['withdrawal']['description']);
		$withdrawal->setDate(date_create_from_format('d/m/Y', $data['withdrawal']['date']));	
		
		Entities::persist($withdrawal);
		Entities::flush();

		return $withdrawal->getId();
		
	}	
	
}
