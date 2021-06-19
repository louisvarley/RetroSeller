<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * The namespaces provided by the SDK.
 */
use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Services;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;
/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Stock extends \App\Controllers\ManagerController
{
	
	protected $authentication = false;
	
	public $page_data = ["title" => "Stock", "description" => "List of items for sale"];		

    /**
     * When the list action is called
     *
     * @return void
     */
	public function listAction(){
		
		$forSaleStatus = findEntity("purchaseStatus", _SALE_STATUS);	
		
		$stock = findBy("purchase", ["status" => $forSaleStatus ]);
		

		View::renderTemplate($this->route_params['controller'] . '/list.html', array_merge(
			$this->route_params, 
			$this->page_data,
			array("entities" => $stock)
		));

			
	
	}
	
}
	

