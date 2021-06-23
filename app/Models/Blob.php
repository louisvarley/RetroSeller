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

		return stream_get_contents($this->data);

	}

}