<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Blob extends \App\Controllers\ManagerController
{
	

	public function indexAction(){
		
		header('Content-type:image/jpg');

		$blob = findEntity($this->route_params['controller'], $this->route_params['id']);

		echo base64_decode($blob->getBase64());
	} 
	
}
