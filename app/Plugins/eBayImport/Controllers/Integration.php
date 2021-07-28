<?php

namespace App\Plugins\eBayImport\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use \Core\Services\ToastService as Toast;

use \App\Plugins\eBayImport\Services\EbayService as eBay;
use \Core\Services\entityService as Entities;


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
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$integration = Entities::findEntity($this->route_params['controller'], $id);

		$integration->setUserId($data['integration']['userId']);
		$integration->setDevId($data['integration']['devId']);
		$integration->setAppId($data['integration']['appId']);
		$integration->setCertId($data['integration']['certId']);
		$integration->setRuName($data['integration']['ruName']);
	
		Entities::persist($integration);
		Entities::flush();
		
	}
	
	public function insertEntity($data){

		$integration = new \App\Models\Integration();

		$integration->setUserId($data['integration']['userId']);
		$integration->setDevId($data['integration']['devId']);
		$integration->setAppId($data['integration']['appId']);
		$integration->setCertId($data['integration']['certId']);	
		$integration->setRuName($data['integration']['ruName']);
		
		Entities::persist($integration);
		Entities::flush();

		return $integration->getId();
		
	}	
	
	/* Authenticate an Integration */
	public function authAction(){
		
		if(isset($this->get['state'])){
			
			$integration = Entities::findEntity($this->route_params['controller'], $this->get['state']);
			
			if($integration == null){
				die("No Integration found with ID " . $this->get['state']);
			}
			
			$response = $integration->requestAccessToken($this->get['code']);
			
			if($response->getStatusCode() !== 200){
				
				toast::throwSuccess($response->error, $response->error_description);
			}else{
				
				toast::throwSuccess("Success...", "You Authenticated eBay to use your RetroSeller App");
			}
			
			header('Location: /integration/list');
			
		}else{
				
			header("Location:" . eBay::withIntegration($this->route_params['id'])::authUrl($this->route_params['id']));
			
		}
		
	}
	
}
