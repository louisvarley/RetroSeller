<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;

use \App\Plugins\eBayImport\Services\EbayService as eBay;
use \Core\Services\entityService as Entities;

/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Auction extends \Core\Controller
{
	
	protected $authentication = true;	
	public $page_data = ["title" => "eBay", "description" => "eBay Selling"];	
	

	
	public function sellingAction(){

		$auctions = [];
		
		foreach(Entities::findAll("integration") as $integration){
				
			$auctions[$integration->getUserId()] = eBay::withIntegration($integration->getId())::getMyActiveAuctions();
		}


		$this->render($this->route_params['controller'] . '/list.html', array("activeList" => $auctions),);

	} 
	
	public function orderAction(){
		
		$id = $this->route_params['id'];

		$results = [];
		
		/* Check all Intergrations */
		foreach(Entities::findAll("integration") as $integration){
			$results[] = eBay::withIntegration($integration->getId())::getOrder($id);
		}
		
		if($results){
		
			$order = end($results)[0];	
	
			
			$this->render("Auction" . '/order.html', array("order" => $order, 'countries' => \Core\Classes\Countries::fetch()),);
		
		}

		
	}	
	
}
