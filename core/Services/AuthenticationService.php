<?php

namespace Core\Services;

class AuthenticationService{
	
	protected static $instance = null;
		
	/**
	 * 
	 * @return CLASS INSTANCE
	 */ 
    public static function instance() {

        if ( null == static::$instance ) {
            static::$instance = new static();
        }

        return static::$instance;
    }	
	
	public function validApiKey(){
			
		if(!isset($_GET['apikey'])) return false;
		
		if(count(findBy("user", ["apikey" => $_GET['apikey']])) > 0){
			return true;
		}
		
		return false;
		
	}
	
	public function loggedIn(){
		
		
		if(false == sessionService()->isset("user"))
			return false;
			
		if(sessionService()->isset("user") && time() - sessionService()->load("activity") > 1800){
			$this->logout();
			toastService()->throwError("Logged Out", "Logged Out due to idle activity");
		}
		
		if(sessionService()->isset("user")){
			return true;
		}
		
	}
	
	public function login($user){
		sessionService()->save("user", $user);		
	}
	
	public function logout(){
		sessionService()->destroy();
	}

	public function me(){

		if($this->loggedIn()){
			

			return findEntity("user",sessionService()->load("user")->getId());
		}

	}
}