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
 

class Transfer extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Transfer", "description" => "Transfers are money transfered between one account to another"];	

	public function getEntity($id = 0){

		return array(
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id),			
			"accounts" => Entities::createOptionSet('Account', 'id','name'),				
		);	
	} 

	public function updateEntity($id, $data){
		
		$transfer = Entities::findEntity($this->route_params['controller'], $id);
		$accountTo = Entities::findEntity("Account", $data['transfer']['account_to_id']);
		$accountFrom = Entities::findEntity("Account", $data['transfer']['account_from_id']);
		
		
		$transfer->setName($data['transfer']['name']);
		$transfer->setAmount($data['transfer']['amount']);
		$transfer->setDate(date_create_from_format('d/m/Y', $data['transfer']['date']));			
		$transfer->setAccountFrom($accountFrom);
		$transfer->setAccountTo($accountTo);	
		

		Entities::persist($transfer);
		Entities::flush();
		
	}
	
	public function insertEntity($data){

		$transfer = new \App\Models\Transfer();

		$accountTo = Entities::findEntity("Account", $data['transfer']['account_to_id']);
		$accountFrom = Entities::findEntity("Account", $data['transfer']['account_from_id']);
		
		$transfer->setName($data['transfer']['name']);
		$transfer->setAmount($data['transfer']['amount']);
		$transfer->setDate(date_create_from_format('d/m/Y', $data['transfer']['date']));			
		$transfer->setAccountFrom($accountFrom);
		$transfer->setAccountTo($accountTo);	
		

		Entities::persist($transfer);
		Entities::flush();

		return $transfer->getId();
		
	}	
	
}
