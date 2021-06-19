<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class PurchaseCategory extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Purchase Category", "description" => "Classify Purchases by category"];	
	
	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id),
			"categories" => createOptionSet('purchaseCategoryView', 'id', 'path'),			
		);	
	} 

	public function updateEntity($id, $data){
		
		$category = findEntity($this->route_params['controller'], $id);

		$category->setName($data[$this->route_params['controller']]['name']);
		$category->setColor($data[$this->route_params['controller']]['color']);
		$category->setParent(findEntity($this->route_params['controller'], $data[$this->route_params['controller']]['parent_id']));
	
		entityManager()->persist($category);
		entityManager()->flush();
		
	}
	
	public function insertEntity($data){

		$category = new \App\Models\PurchaseCategory();

		$category->setName($data[$this->route_params['controller']]['name']);
		$category->setColor($data[$this->route_params['controller']]['color']);
		$category->setParent(findEntity($this->route_params['controller'], $data[$this->route_params['controller']]['parent_id']));
	
		entityManager()->persist($category);
		entityManager()->flush();

		return $category->getId();
		
	}	
	
}
