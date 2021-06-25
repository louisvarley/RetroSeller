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

        ebayService()->CreateAuctionsFromActiveAuctions();
        die();



		View::renderTemplate($this->route_params['controller'] . '/list.html', array_merge(
			$this->route_params, 
			$this->page_data,
			array("activeList" => eBayService()->getMyActiveAuctions()),
		));

	} 
	
}
