<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Purchases extends \App\Controllers\Api
{
	
	protected function purchaseImagePostAction(){

		try{


			$imageLocation = ($_FILES['images']['tmp_name'][0]);
			$imageData = file_get_contents($imageLocation);
			$imageBase64 = base64_encode($imageData);

			$purchase = findEntity("purchase",$this->get['purchaseId']);

			$image = new \App\Models\Blob();
			$image->setData($imageBase64);
			entityService()->persist($image);

			$purchase->getImages()->add($image);

			entityService()->flush();

			return new \Core\Classes\ApiResponse(200, 0, ['blobId' => $image->getId(), 'message' => 'Image Saved']);
	
		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);
		}
	}


	protected function purchaseImageDeleteAction(){

		try{

			$purchase = findEntity("purchase", $this->get['purchaseId']);
			$image = findEntity("blob", $this->get['blobId']);

			$purchase->getImages()->removeElement($image);

			entityService()->remove($image);
			entityService()->flush();

			
			return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Image deleted']);

		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);

		}
	}


	protected function purchaseImageRotateGetAction(){
		

		
		$blobId = $this->get['blobId'];
		$image = findEntity("blob", $blobId);
		$image->rotate();
	
		return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Image Saved']);
		
	}


	protected function datatableGetAction(){
		
	foreach($_GET['columns'] as $key => $column){
		$_GET['columns'][$key]['data'] = substr($_GET['columns'][$key]['data'], 2);
	}

	$datatables = (new \Doctrine\DataTables\Builder())
    ->withColumnAliases([
        'id' => 'u.id',
		'name' => 'u.name',
		'status' => 'u.status',
		'category' => 'u.category',
    ])
    ->withIndexColumn('u.id')
    ->withQueryBuilder(
        entityService()->createQueryBuilder()
            ->select('u')
            ->from(_MODELS . "purchase", 'u')
			->join('u.status', 's')
			->join('u.category', 'c')
			->addSelect('s,c')
		)
    ->withRequestParams($_GET);

	return ($datatables->getResponse());		
	

	}

}
