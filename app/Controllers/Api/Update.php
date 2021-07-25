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

	public function GitGetAction(){
		$message = "";
		$output = shell_exec('git checkout ' . DIR_ROOT);
		$message .= $output;

		$output = shell_exec('git fetch ' . DIR_ROOT);
		$message .= $output;
		
		$output = shell_exec('git pull '  . DIR_ROOT);
		$message .= $output;		
		
		$output = shell_exec('chmod +x '  . DIR_ROOT . '/.update.sh');
		$message .= $output;			

		return new \Core\Classes\ApiResponse(200, 0, ['message' => $message]);

	}


}
