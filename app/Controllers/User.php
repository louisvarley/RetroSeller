<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use \Core\Services\ToastService as Toast;
use \Core\Services\EntityService as Entities;

/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class User extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Users", "description" => "Users who can access RetroSeller"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id)			
		);	
	} 

	public function updateEntity($id, $data){
		
		$user = Entities::findEntity($this->route_params['controller'], $id);
		$user->setEmail($data['user']['email']);
		
		if(isset($data['user']['password']) && strlen($data['user']['password']) > 5){
			
			if($data['user']['password'] != $data['user']['password_confirm']){
				toast::throwError("Error...", "Password Mismatch");
				return;
			}
			
			$user->setPassword($data['user']['password']);
		}

		Entities::persist($user);
		Entities::flush();
		
	}
	
	public function insertEntity($data){

		$user = new \App\Models\User();

		$user->setEmail($data['user']['email']);
		
		if($data['user']['password'] != $data['user']['password_confirm']){
			toast::throwError("Error...", "Password Mismatch");
			return;
		}
		
		$user->setPassword($data['user']['password']);
		$user->generateApiKey();
		
		Entities::persist($user);
		Entities::flush();

		return $user->getId();
		
	}	
	
}
