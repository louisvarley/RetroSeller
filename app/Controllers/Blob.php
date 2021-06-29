<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Blob extends \App\Controllers\ManagerController
{

	protected $authentication = false;	

	public function indexAction(){
		
		header('Content-type:image/jpg');

		$blob = findEntity($this->route_params['controller'], $this->route_params['id']);

		if(array_key_exists("size", $this->route_params)){
			
			$imageSize = _IMAGE_SIZES[$this->route_params['size']];
			echo base64_decode($this->base64_resize($blob->getBase64(), $imageSize['width'], $imageSize['height']));
			
		}else{ /* Full Size */
			
			echo base64_decode($blob->getBase64());
			
		}

	} 
	
	public function purchaseAction(){
		
		
		header('Content-type:image/jpg');
	
		$purchase = findEntity("purchase", $this->route_params['id']);
		
		$blob = $purchase->getImages()->first();
		
		$imageSize = _IMAGE_SIZES[$this->route_params['size']];

		if(empty($blob)){
			echo base64_decode($this->base64_resize(base64_encode(file_get_contents(DIR_STATIC . '/img/place-holder.jpg')), $imageSize['width'], $imageSize['height']));
			
		}else{
			echo base64_decode($this->base64_resize($blob->getBase64(), $imageSize['width'], $imageSize['height']));
		}

		
	} 	
	
	
	public function base64_resize($image, $width, $height){
		
		$im = imagecreatefromstring(base64_decode($image));
		$source_width = imagesx($im);
		$source_height = imagesy($im);
		$ratio =  $source_height / $source_width;

		$new_width = $width; // assign new width to new resized image
		$new_height = $ratio * $height;

		$thumb = imagecreatetruecolor($new_width, $new_height);

		$transparency = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
		imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $transparency);

		imagecopyresampled($thumb, $im, 0, 0, 0, 0, $new_width, $new_height, $source_width, $source_height);
		
		imagedestroy($im);
		ob_start();
		imagejpeg($thumb);
		$contents =  ob_get_clean();		
		return base64_encode($contents);
		
	}
	
}
