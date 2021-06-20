<?php

namespace Core;

class ToastManager{
	
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
	
	public function __construct(){
		
		if(sessionManager()->load("toasts") == null){
			sessionManager()->save("toasts",array());
		}
		
	}
	
	public function clear(){	
		sessionManager()->save("toasts",array());		
	}
	
	public function throwSuccess($title, $text){
		sessionManager()->append("toasts",array(
			"title" => $title,
			"type" => "success",
			"text" => $text
		));		
		
	}
	
	public function throwError($title, $text){
		sessionManager()->append("toasts",array(
			"title" => $title,
			"type" => "error",
			"text" => $text
		));				
	}
	
	public function throwWarning($title, $text){
		sessionManager()->append("toasts",array(
			"title" => $title,
			"type" => "warning",
			"text" => $text
		));				
	}
	
	public function getToasts(){
		
		return sessionManager()->load("toasts");
	}
	
}