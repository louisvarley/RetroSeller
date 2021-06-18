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

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
		
		if(authenticationManager()->loggedIn())
			header('Location: /');
		
		if($this->isPOST()){

			$user = findBy("user", ['email' => $this->post['email']]);
			 
			if(count($user) > 0 && $user[0]->validatePassword($this->post['password'])){
				
				authenticationManager()->login($user[0]);
				header('Location: /');
				
			}else{
			
				toastManager()->throwError("Error...", "Your login details were incorrect or not found");
			
			}
		}
		

        View::renderTemplate('Login/index.html');
    }
}
