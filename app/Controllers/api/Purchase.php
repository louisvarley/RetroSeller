<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class PurchaseApi extends \App\Controllers\Api\ApiController
{
	
	protected function purchaseImagePostAction(){

		try{

			$imageLocation = ($_FILES['image']['tmp_name']);
			$imageData = file_get_contents($imageLocation);
			$imageBase64 = base64_encode($imageData);

			$purchase = findEntity("purchase",$this->get['purchaseId']);

			$image = new \App\Models\Blob();
			$image->setData($imageBase64);
			entityManager()->persist($image);

			$purchase->getImages()->add($image);

			entityManager()->flush();

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

			entityManager()->remove($image);
			entityManager()->flush();

			
			return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Image deleted']);

		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);

		}
	}

}
