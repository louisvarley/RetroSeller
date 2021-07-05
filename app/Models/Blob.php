<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_blobs")
 */
class Blob
{
	/**
    * @ORM\Id
    * @ORM\Column(type="integer", name="id")
    * @ORM\GeneratedValue
    */
    protected $id;	
	
	/** @ORM\Column(type="blob", name="data") **/
    protected $data;
	

    public function getId()
    {
        return $this->id;
    }

    public function getData()
    {
        return $this->data;
    }  

    public function setData($data)
    {
        return $this->data = $data;
    }  


	public function getBase64(){

		if(is_resource($this->data)){
			rewind($this->data);			
			return stream_get_contents($this->data);			
		}
		
		return $this->data;

	}

	public function getUrl(){
		return "/blob/" . $this->getId() . ".jpg";
	}
	
	public function getThumbnailUrl(){
		return "/blob/thumbnail/" . $this->getId() . ".jpg";
	}	
	
	public function getSmallUrl(){
		return "/blob/small/" . $this->getId() . ".jpg";
	}	
	
	public function rotate(){
		

		$imageBase64 = $this->getBase64();

		$res = imagecreatefromstring(base64_decode($imageBase64));

		if ($res === false) exit;
		$rotated = imagerotate($res, -90, 0);
	
		ob_start(); 
			imagejpeg($rotated); 
			$imageBase64 = base64_encode(ob_get_contents()); 
		ob_end_clean(); 	
		
		$this->setData($imageBase64);
		EntityService()->persist($this);
		EntityService()->flush();

	}

}