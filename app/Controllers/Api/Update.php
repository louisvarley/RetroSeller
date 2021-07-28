<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;
use \Core\Services\UpdateService as Updater

/**
 * Home controller
 *
 * PHP version 7.0
 */

class Update extends \App\Controllers\Api
{

	public function CurrentVersionGetAction(){


		return new \Core\Classes\ApiResponse(200, 0, ['version' => Updater::currentVersion()]);

	}
	
	public function RemoteVersionGetAction(){


		return new \Core\Classes\ApiResponse(200, 0, ['version' => Updater::remoteVersion()]);

	}	


}
