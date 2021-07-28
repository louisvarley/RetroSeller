<?php

namespace Core\Services;

class SessionService{
	

	public static function isset($key){
		if(isset($_SESSION[$key])){
			return true;
		}
		return false;
	
	}
	
	public static function activity(){
		
		self::save("activity", time());
	}
	
	public static function save($key, $value){
		$_SESSION[$key] = $value;
	}
	
	public static function append($key, $value){
		if(self::isset($key)){
			array_push($_SESSION[$key], $value);			
		}else{
			self::save($key, $value);
		}
		
	}		
	
	public static function load($key){
		if(self::isset($key)){
			return $_SESSION[$key];	
		}
	}
	
	public static function destroy(){
		$_SESSION = array();
		session_destroy();
		session_start();			
	}

	public static function start(){
		session_start();
	}	
	
}