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
 

class Settings extends \Core\Controller
{

	protected $authentication = true;	
	public $page_data = ["title" => "Settings", "description" => "Config Settings"];	
	
	public function editAction(){


		if($this->isPOST()){
			$this->save($this->post);
		}


		$setingsEntities = Entities::findAll("Metadata");

		$settings = [];

		foreach($setingsEntities as $entity){

			$settings[$entity->getKey()] = $entity->getValue();
		}

		View::renderTemplate('Settings/form.html', array_merge(
				$this->route_params, 
				$this->page_data,
				['settings' => $settings],
				["saleVendors" => Entities::createOptionSet('SaleVendor', 'id','name')],
				["saleStatuses" => Entities::createOptionSet('SaleStatus', 'id','name')],
				["purchaseStatuses" => Entities::createOptionSet('PurchaseStatus', 'id','name')],				
				["paymentVendors" => Entities::createOptionSet('PaymentVendor', 'id','name')],						
			));
	} 



	public function save($data){
		

		foreach($data['settings'] as $key => $value){
			setMetadata($key, $value);
		}

		
	}
	

	
}
