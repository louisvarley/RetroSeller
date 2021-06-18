<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="rs_purchase_categories_vw")
 */
class PurchaseCategoryView
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
     * @ORM\OneToOne(targetEntity="PurchaseCategoryView")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;	
	
	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $path;	

	/**
    * @ORM\Column(type="integer", name="depth")
    */
    protected $depth;	

	private function __construct() {}	
	
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getDepth()
    {
        return $this->depth;
    }	
	
    public function getPath()
    {
        return $this->path;
    }	
}