<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Integration extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "eBay integration", "description" => "eBay integration Settings"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$integration = findEntity($this->route_params['controller'], $id);

		$integration->setUserId($data['integration']['userId']);
		$integration->setDevId($data['integration']['devId']);
		$integration->setAppId($data['integration']['appId']);
		$integration->setCertId($data['integration']['certId']);
		$integration->setRuName($data['integration']['ruName']);
	
		entityService()->persist($integration);
		entityService()->flush();
		
	}
	
	public function insertEntity($data){

		$integration = new \App\Models\Integration();

		$integration->setUserId($data['integration']['userId']);
		$integration->setDevId($data['integration']['devId']);
		$integration->setAppId($data['integration']['appId']);
		$integration->setCertId($data['integration']['certId']);	
		$integration->setRuName($data['integration']['ruName']);
		
		entityService()->persist($integration);
		entityService()->flush();

		return $integration->getId();
		
	}	
	
	/* Authenticate an Integration */
	public function authAction(){
		
		if(isset($this->get['state'])){
			
			$integration = findEntity($this->route_params['controller'], $this->get['state']);
			
			if($intergration == null){
				 
				die("No Intergration found with ID " . $this->get['state']);
				
			}
			
			$response = $integration->requestAccessToken($this->get['code']);
			
			if($response->getStatusCode() !== 200){
				
				toastService()->throwSuccess($response->error, $response->error_description);
			}else{
				
				toastService()->throwSuccess("Success...", "You Authenticated eBay to use your RetroSeller App");
			}
			
			header('Location: /integration/list');
			
		}else{
				
			header("Location:" . ebayService($this->route_params['id'])->authUrl($this->route_params['id']));
			
		}
		
	}
	
}
