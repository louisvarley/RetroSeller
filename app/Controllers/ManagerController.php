<?php

namespace App\Controllers;

use \Core\View;
use \App\Models;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class ManagerController extends \Core\Controller
{
	
	protected $authentication = true;
	public $page_data = ["title" => "", "description" => ""];
	
	/**
	 * ------------------------------------------------------
     * Entity Actions
	 * ------------------------------------------------------
     */		
	
    /**
     * Default will pull just the current controllers model
     *
     * @return array containing the result
     */
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => entityManager()->find(_MODELS . $this->route_params['controller'], $id)
		);	
	} 
	
    /**
     * Extended controllers use to update entities
     *
     * @return void
     */	
	public function updateEntity($id, $data){
		
	}

    /**
     * Extended controllers use to insert entities
     *
     * @return id
     */		
	public function insertEntity($data){ 
		return 0;
	}

    /**
     * Extended controllers use to delete entities
     *
     * @return void
     */		
	public function deleteEntity($id){
		
		$entity = entityManager()->find(_MODELS . $this->route_params['controller'], $this->route_params['id']);
		entityManager()->remove($entity);
		entityManager()->flush();
		
	}
    
	/**
	 * ------------------------------------------------------
     * Controller Actions
	 * ------------------------------------------------------
     */	

    /**
     * When the new action is called
     *
     * @return void
     */		
	public function newAction(){
		
		/* ON POST */
		if($this->isPOST() && !array_key_exists("id", $this->route_params)){
			$id = $this->insertEntity($this->post);
			header('Location: /'. $this->route_params['controller'] . '/edit/' . $id);
			die();
		}	
		
		/* ON GET */
		if($this->isGET()){
			View::renderTemplate($this->route_params['controller'] . '/form.html', array_merge(
				$this->route_params,
				$this->page_data,
				$this->getEntity(),
			));
		}
	}

    /**
     * When the delete action is called
     *
     * @return void
     */		
	public function deleteAction(){

		/* ON GET */
		if($this->isGET()){
			
			$entity = entityManager()->find(_MODELS . $this->route_params['controller'], $this->route_params['id']);
			$this->deleteEntity($entity);
			header('Location: /'. $this->route_params['controller'] . '/list');
			die();
		}			
		
	}

    /**
     * When the edit action is called
     *
     * @return void
     */
    public function editAction(){
		
		/* ON UPDATE */
		if($this->isPOST() && array_key_exists("id", $this->route_params)){
			$this->updateEntity($this->route_params['id'], $this->post);
			toastManager()->throwSuccess("Saved...", "Your changes were saved");
			header('Location: /'. $this->route_params['controller'] . '/edit/' . $this->route_params['id']);
			die();
		}			
		
		/* ON GET */
		if($this->isGET()){
			View::renderTemplate($this->route_params['controller'] . '/form.html', array_merge(
				$this->route_params, 
				$this->page_data,
				$this->getEntity($this->route_params['id']),
			));
		}
	}	

    /**
     * When the list action is called
     *
     * @return void
     */
	public function listAction(){

		View::renderTemplate($this->route_params['controller'] . '/list.html', array_merge(
			$this->route_params, 
			$this->page_data,
			array("entities" => findAll(_MODELS . $this->route_params['controller']));
		));

	}	
		
}
