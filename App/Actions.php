<?php

use \Core\EntityManager;

jobManager()->Add(function(){
	
	$sql = '	
		
	CREATE OR REPLACE VIEW rs_purchase_categories_vw AS
	SELECT   id,
			 name,
			 parent_id,
			 path,
			 depth,
			 color
	FROM     (
					SELECT id,
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
					AND       t3.id IS NOT NULL ) a
	ORDER BY a.path,
			 a.name ASC ';
		 		
	$connection = entityManager()->getConnection();
	$statement = $connection->prepare($sql);
	$statement->execute();		 
	
});



jobManager()->Add(function(){
	
	$sql = '	
		
	CREATE OR REPLACE VIEW rs_unsold_purchases_vw AS
	SELECT   id,
			 name,
			 concat(" <span class=\'badge badge-primary\'>", name,"</span> ", "<span class=\'badge badge-info\'>ID:",id,"</span> ","<span class=\'badge badge-secondary\'>", date, "</span>") as optionset_name
			
	from rs_purchases

	WHERE purchase_status_id <> ' . _SOLD_STATUS . '

	ORDER BY id';
		 		
	$connection = entityManager()->getConnection();
	$statement = $connection->prepare($sql);
	$statement->execute();		 
	
});

