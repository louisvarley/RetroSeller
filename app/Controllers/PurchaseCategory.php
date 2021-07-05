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
			"categories" => createOptionSet('purchaseCategory', 'id', 'path'),	
		);	
	} 

	public function updateEntity($id, $data){
		
		$category = findEntity($this->route_params['controller'], $id);

		$category->setName($data[$this->route_params['controller']]['name']);
		$category->setColor($data[$this->route_params['controller']]['color']);
		$category->setParent(findEntity($this->route_params['controller'], $data[$this->route_params['controller']]['parent_id']));
	
		entityService()->persist($category);
		entityService()->flush();
		
		$this->updatePaths();
		
	}
	
	public function insertEntity($data){

		$category = new \App\Models\PurchaseCategory();

		$category->setName($data[$this->route_params['controller']]['name']);
		$category->setColor($data[$this->route_params['controller']]['color']);
		$category->setParent(findEntity($this->route_params['controller'], $data[$this->route_params['controller']]['parent_id']));
	
		entityService()->persist($category);
		entityService()->flush();

		$this->updatePaths();

		return $category->getId();
		
	}	
	
	public function updatePaths(){
		
		
		$sql = 'update rs_purchase_categories c

				JOIN (	SELECT id,
				   name,
				   parent_id,
				   name  AS path,
				   color AS color,
				   0     AS depth
			FROM   rs_purchase_categories
			WHERE  parent_id IS NULL
			UNION ALL
			SELECT    t2.id                                     AS id,
					  t2.name                                   AS name,
					  t1.id                                     AS parent_id,
								concat(t1.name, " > ", t2.name) AS path,
					  t2.color                                  AS color,
					  1                                         AS depth
			FROM      rs_purchase_categories t1
			LEFT JOIN rs_purchase_categories t2
			ON        t2.parent_id = t1.id
			WHERE     t1.parent_id IS NULL
			UNION ALL
			SELECT    t3.id                                                     AS id,
					  t3.name                                                   AS name,
					  t2.id                                                     AS parent_id,
								concat(t1.name, " > ", t2.name, " > ", t3.name) AS path,
					  t3.color                                                  AS color,
					  2                                                         AS depth
			FROM      rs_purchase_categories t2
			LEFT JOIN rs_purchase_categories t3
			ON        t3.parent_id = t2.id
			LEFT JOIN rs_purchase_categories t1
			ON        t2.parent_id = t1.id
			WHERE     t2.parent_id IS NOT NULL
			AND       t3.id IS NOT NULL) a

			SET c.path = a.path
			WHERE c.id = a.id
			and c.id > 0


		';
		

        $statement = entityService()->getConnection()->prepare($sql);
        $statement->execute();

	}
	
}
