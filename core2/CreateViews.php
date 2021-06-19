<?php

/* Is Run on every load so any DB Actions Here */

DbActionsManager()->Add('

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
         a.name ASC 
	
';

