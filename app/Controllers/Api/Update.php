<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Update extends \App\Controllers\Api
{

	public function CurrentVersionGetAction(){
		$message = "";
		
		$version = file_get_contents(DIR_ROOT . '/.git/FETCH_HEAD');		

		return new \Core\Classes\ApiResponse(200, 0, ['version' => $version]);

	}


}
