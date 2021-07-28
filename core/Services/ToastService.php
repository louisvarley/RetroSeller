<?php

namespace Core\Services;

use \Core\Services\SessionService as Session;

class ToastService{
	
	public static function clear(){	
		Session::save("toasts",array());		
	}
	
	public static function throwSuccess($title, $text){
		Session::append("toasts",array(
			"title" => $title,
			"type" => "success",
			"text" => $text
		));		
	}
	
	public static function throwError($title, $text){
		Session::append("toasts",array(
			"title" => $title,
			"type" => "error",
			"text" => $text
		));				
	}
	
	public static function throwWarning($title, $text){
		Session::append("toasts",array(
			"title" => $title,
			"type" => "warning",
			"text" => $text
		));				
	}
	
	public static function getToasts(){
		
		return Session::load("toasts");
	}
	
}