<?php

namespace App\Controllers;

use \Core\View;
use \Core\Services\AuthenticationService as Authentication;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Logout extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
		
		
		Authentication::logout();
		
		header('Location: /');
		
	}
}
