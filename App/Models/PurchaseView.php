<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="rs_unsold_purchases_vw")
 */
class PurchaseView
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
	
	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $optionset_name;	
		
   
	private function __construct() {}	
	
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOptionsetName()
    {
        return $this->optionset_name;
    }

    
}