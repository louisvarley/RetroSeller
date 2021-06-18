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
}