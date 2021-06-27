<?php


namespace Core;

class AuthenticationManager{
	
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
		
		
		if(false == sessionManager()->isset("user"))
			return false;
			
		if(sessionManager()->isset("user") && time() - sessionManager()->load("activity") > 1800){
			$this->logout();
			toastManager()->throwError("Logged Out", "Logged Out due to idle activity");
		}
		
		if(sessionManager()->isset("user")){
			return true;
		}
		
	}
	
	public function login($user){
		sessionManager()->save("user", $user);		
	}
	
	public function logout(){
		sessionManager()->destroy();
	}

	public function me(){

		if($this->loggedIn()){
			

			return findEntity("user",sessionManager()->load("user")->getId());
		}

	}
}