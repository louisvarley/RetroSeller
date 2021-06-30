<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Buyout extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Buyout", "description" => "Buyouts allow one account to 'buy out' all the expenses for a purchase but the purchase can never then be sold"];	

	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id),
			"purchases" => createOptionSet('Purchase', 'id',['id','name','date']),		
			"accounts" => createOptionSet('Account', 'id','name'),				
		);	
	} 

	public function updateEntity($id, $data){
		
		$buyout = findEntity($this->route_params['controller'], $id);		
		$account = findEntity("Account", $data['buyout']['account_id']);
		$purchase = findEntity("Purchase", $data['buyout']['purchase_id']);

		$buyout->setDate(date_create_from_format('d/m/Y', $data['buyout']['date']));			
		$buyout->setAccount($account);
		$buyout->setPurchase($purchase);		
		
		$buyout->getPurchase()->setStatus(findEntity("purchaseStatus", _PURCHASE_STATUSES['BOUGHT_OUT']['id']));

		entityManager()->persist($buyout);
		entityManager()->flush();
		
	}
	
	public function insertEntity($data){

		$buyout = new \App\Models\Buyout();
		$account = findEntity("Account", $data['buyout']['account_id']);
		$purchase = findEntity("Purchase", $data['buyout']['purchase_id']);

		$buyout->setDate(date_create_from_format('d/m/Y', $data['buyout']['date']));			
		$buyout->setAccount($account);
		$buyout->setPurchase($purchase);			
		$buyout->getPurchase()->setStatus(findEntity("PurchaseStatus", _PURCHASE_STATUSES['BOUGHT_OUT']['id']));
		
		entityManager()->persist($buyout);
		entityManager()->flush();

		return $buyout->getId();
		
	}	
	
}
