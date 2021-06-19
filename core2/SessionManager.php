<?php


namespace Core;

class SessionManager{
	
	protected static $instance = null;
	
	public function __construct(){
	
		
	}
	
	public static function start(){
		session_start();	
	}
	
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
	
	public function isset($key){
		if(isset($_SESSION[$key])){
			return true;
		}
		return false;
	
	}
	
	public function activity(){
		
		$this->save("activity", time());
	}
	
	public function save($key, $value){
		$_SESSION[$key] = $value;
	}
	
	public function append($key, $value){
		if($this->isset($key)){
			array_push($_SESSION[$key], $value);			
		}else{
			$this->save($key, $value);
		}
	}		
	
	public function load($key){
		if($this->isset($key)){
			return $_SESSION[$key];	
		}
	}
	
	public function destroy(){
		$_SESSION = array();
		session_destroy();
		session_start();			
	}	
	
}