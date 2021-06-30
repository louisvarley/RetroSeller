<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Auction extends \Core\Controller
{
	
	protected $authentication = true;	
	public $page_data = ["title" => "Settings", "description" => "Config Settings"];	
	

	
	public function sellingAction(){

		$auctions = [];
		
		foreach(findAll("integration") as $integration){
				
			$auctions[$integration->getUserId()] = eBayService($integration->getId())->getMyActiveAuctions();
		}


		$this->render($this->route_params['controller'] . '/list.html', array("activeList" => $auctions),);

	} 
	
}
