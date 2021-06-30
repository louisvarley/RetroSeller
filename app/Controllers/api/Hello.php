<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Hello extends \App\Controllers\Api
{

	public function helloGetAction(){

		return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Hello World']);

	}
	
	protected function helloAuthGetAction(){

		return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Hello World']);

	}	

}
