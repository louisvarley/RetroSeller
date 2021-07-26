<?php

namespace App\Controllers;

use \Core\View;

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
		
		if(authenticationService()->loggedIn())
			header('Location: /');
		
		if($this->isPOST()){

			$user = findBy("User", ['email' => $this->post['email']]);
			 
			if(count($user) > 0 && $user[0]->validatePassword($this->post['password'])){
				
				authenticationService()->login($user[0]);
				if($this->get['redirect']){
					header('Location:' . urldecode($this->get['redirect']));
					die();
				}else{
					header('Location: /');
				}
				
				
			}else{
			
				toastService()->throwError("Error...", "Your login details were incorrect or not found");
			
			}
		}
		

		$this->render('Login/index.html');

    }
}
