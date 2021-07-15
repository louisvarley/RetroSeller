<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Transfer extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Transfer", "description" => "Transfers are money transfered between one account to another"];	

	public function getEntity($id = 0){

		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id),			
			"accounts" => createOptionSet('Account', 'id','name'),				
		);	
	} 

	public function updateEntity($id, $data){
		
		$transfer = findEntity($this->route_params['controller'], $id);
		$accountTo = findEntity("Account", $data['transfer']['account_to_id']);
		$accountFrom = findEntity("Account", $data['transfer']['account_from_id']);
		
		
		$transfer->setName($data['transfer']['name']);
		$transfer->setAmount($data['transfer']['amount']);
		$transfer->setDate(date_create_from_format('d/m/Y', $data['transfer']['date']));			
		$transfer->setAccountFrom($accountFrom);
		$transfer->setAccountTo($accountTo);	
		

		entityService()->persist($transfer);
		entityService()->flush();
		
	}
	
	public function insertEntity($data){

		$transfer = new \App\Models\Transfer();

		$accountTo = findEntity("Account", $data['transfer']['account_to_id']);
		$accountFrom = findEntity("Account", $data['transfer']['account_from_id']);
		
		$transfer->setName($data['transfer']['name']);
		$transfer->setAmount($data['transfer']['amount']);
		$transfer->setDate(date_create_from_format('d/m/Y', $data['transfer']['date']));			
		$transfer->setAccountFrom($accountFrom);
		$transfer->setAccountTo($accountTo);	
		

		entityService()->persist($transfer);
		entityService()->flush();

		return $transfer->getId();
		
	}	
	
}
