<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Purchase;
use Doctrine\DataTables;
/**
 * Home controller
 *
 * PHP version 7.0
 */
class Ajax extends \Core\Controller
{

	protected function before()
	{
		//header('Content-type: application/json');
	}

}
