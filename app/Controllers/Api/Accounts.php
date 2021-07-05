<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Accounts extends \App\Controllers\Api
{
	
	protected function accountLogoPostAction(){

		try{


			$imageLocation = ($_FILES['images']['tmp_name'][0]);
			$imageData = file_get_contents($imageLocation);
			$imageBase64 = base64_encode($imageData);

			$account = findEntity("account",$this->get['accountId']);

			$image = new \App\Models\Blob();
			$image->setData($imageBase64);
			entityService()->persist($image);

			$account->setLogo($image);

			entityService()->flush();

			return new \Core\Classes\ApiResponse(200, 0, ['blobId' => $image->getId(), 'message' => 'Image Saved']);
	
		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);
		}
	}


	protected function accountLogoDeleteAction(){

		try{

			$account = findEntity("account", $this->get['accountId']);
			$image = findEntity("blob", $this->get['blobId']);

			entityService()->remove($account->getLogo());
			$account->setLogo(null);
			
			entityService()->flush();
			
			return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Image deleted']);

		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);

		}
	}


	protected function accountLogoRotateGetAction(){
		

		
		$blobId = $this->get['blobId'];
		$image = findEntity("blob", $blobId);
		$image->rotate();
	
		return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Image Saved']);
		
	}


}
