<?php

namespace App\Controllers;

use \Core\View;
use \Core\Services\AuthenticationService as Authentication;
use \Core\Services\ToastService as Toast;
use \Core\Services\EntityService as Entities;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{

	public $page_data = ["title" => "Login", "description" => ""];

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
		
		if(Authentication::loggedIn())
			header('Location: /');
		
		if($this->isPOST()){

			$user = Entities::findBy("User", ['email' => $this->post['email']]);
			 
			if(count($user) > 0 && $user[0]->validatePassword($this->post['password'])){
				
				Authentication::login($user[0]);
				if(isset($this->get['redirect'])){
					header('Location:' . urldecode($this->get['redirect']));
					die();
				}else{
					header('Location: /');
				}
				
				
			}else{
			
				Toast::throwError("Error...", "Your login details were incorrect or not found");
			
			}
		}
		

		$this->render('Login/index.html');

    }
}
