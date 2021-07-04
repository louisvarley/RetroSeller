<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Invoice extends \Core\Controller
{
	
	public $page_data = ["title" => "Invoice", "description" => "Generate an Invoice"];		

	public function indexAction($id = 0){
		
		
		View::renderTemplate('Invoice/index.html');
		
	} 

	
	
}
