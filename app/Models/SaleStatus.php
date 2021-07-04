<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_sale_statuses")
 */
class SaleStatus
{
	/* Static Statuses */
	public static function Paid() {return findEntity("SaleStatus", _SALE_STATUSES['PAID']['id']);}
	public static function Pending() {return findEntity("SaleStatus", _SALE_STATUSES['PENDING']['id']);}	
	public static function Cancelled() {return findEntity("SaleStatus", _SALE_STATUSES['CANCELLED']['id']);}	
	public static function Dispatched() {return findEntity("SaleStatus", _SALE_STATUSES['DISPATCHED']['id']);}
	
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