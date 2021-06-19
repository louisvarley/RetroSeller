<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_purchase_statuses")
 */
class PurchaseStatus
{
	/**
    * @ORM\Id
    * @ORM\Column(type="integer", name="id")
    * @ORM\GeneratedValue
    */
    protected $id;	
	
	
	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $name;	
	
	
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setname($name)
    {
        $this->name = $name;
    }
	
}