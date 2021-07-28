<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use \Core\Services\entityService as Entities;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_purchase_statuses")
 */
class PurchaseStatus
{
	
	/* Static Statuses */
	public static function ForSale() {return Entities::findEntity("PurchaseStatus", _PURCHASE_STATUSES['FOR_SALE']['id']);}
	public static function Held() {return Entities::findEntity("PurchaseStatus", _PURCHASE_STATUSES['HELD']['id']);}	
	public static function WrittenOff() {return Entities::findEntity("PurchaseStatus", _PURCHASE_STATUSES['WRITTEN_OFF']['id']);}	
	public static function RequiresRepair() {return Entities::findEntity("PurchaseStatus", _PURCHASE_STATUSES['REQUIRES_REPAIR']['id']);}	
	public static function Sold() {return Entities::findEntity("PurchaseStatus", _PURCHASE_STATUSES['SOLD']['id']);}	
	public static function BoughtOut() {return Entities::findEntity("PurchaseStatus", _PURCHASE_STATUSES['BOUGHT_OUT']['id']);}		
		
	
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