<?php

namespace Core\Services;

class ToastService{
	
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
		
		if(sessionService()->load("toasts") == null){
			sessionService()->save("toasts",array());
		}
		
	}
	
	public function clear(){	
		sessionService()->save("toasts",array());		
	}
	
	public function throwSuccess($title, $text){
		sessionService()->append("toasts",array(
			"title" => $title,
			"type" => "success",
			"text" => $text
		));		
		
	}
	
	public function throwError($title, $text){
		sessionService()->append("toasts",array(
			"title" => $title,
			"type" => "error",
			"text" => $text
		));				
	}
	
	public function throwWarning($title, $text){
		sessionService()->append("toasts",array(
			"title" => $title,
			"type" => "warning",
			"text" => $text
		));				
	}
	
	public function getToasts(){
		
		return sessionService()->load("toasts");
	}
	
}