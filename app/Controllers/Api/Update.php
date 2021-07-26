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


		return new \Core\Classes\ApiResponse(200, 0, ['version' => updateService()->currentVersion()]);

	}
	
	public function RemoteVersionGetAction(){


		return new \Core\Classes\ApiResponse(200, 0, ['version' => updateService()->remoteVersion()]);

	}	


}
