<?php

namespace App\Controllers;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Api extends \Core\Controller
{

	protected function before()
	{
		header('Content-type: application/json');
	}
	
	public function apiCheck(){
		
		if(authenticationManager()->validApiKey()){
			return true;
		}
		
	}
	
    public function __call($name, $args)
    {

		if($this->isPOST()){
			$method = $name . "Post";
		}

		if($this->isGET()){
			$method = $name . "Get";
		}

		if($this->isPUT()){
			$method = $name . "Put";
		}		

		if($this->isDELETE()){
			$method = $name . "Delete";
		}	
		
        $method .= 'Action';
		$reflection = new \ReflectionMethod($this, $method);		
		
		/* Update Last Activity Time */
		sessionManager()->activity();
		
		/* Method Not Found */
        if (!method_exists($this, $method)) {
			$response = new \Core\Classes\ApiResponse(404, 404, ['message' => "API Method $method not found in " . get_class($this)]);
			
		/* Method found, but protected and no authentication or API Key */	
		}elseif($reflection->isProtected() && (!$this->apiCheck() && !authenticationManager()->loggedIn())){
			$response = new \Core\Classes\ApiResponse(401, 401, ['message' => "UnAuthorised or invalid API Key "]);
			
		/* Fine to Run */
		}else{
			
			$response = call_user_func_array([$this, $method], $args);
		}
		
		if ($this->before() !== false) {
			
			if(method_exists($response,"as_json")){
				echo $response->asJson();
			}else{
				echo json_encode($response);
			}
		
			$this->after();
		}		
			

    }
	
}
