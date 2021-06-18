<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_purchase_categories")
 */
class PurchaseCategory
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
    protected $color;	
	
	
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseCategory")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;	
	
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseCategoryView", inversedBy="id" )
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    protected $view;		
	
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
	

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }	
	
    public function getView()
    {
        return $this->view;
    }	
	
    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }	
	
}