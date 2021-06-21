<?php
namespace App\Models;

use Doctrine\ORM\Query\ResultSetMapping;
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
    * @ORM\Column(type="string", nullable="false", name="path")
    */
    protected $path;	
	
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseCategory")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;	


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
	
	public function getPath(){
		return $this->path;
	}

}