<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use \Core\Services\EntityService as Entities;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_purchase_groups")
 */
class PurchaseGroup
{
	/**
    * @ORM\Id
    * @ORM\Column(type="integer", name="id")
    * @ORM\GeneratedValue
    */
    protected $id;	
	
	/**
    * @ORM\Column(type="string")
    */
    protected $name;
	
	/**
    * @ORM\Column(type="string")
    */
    protected $description;
	
    /**
     * One purchase group has many purchases. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Purchase", mappedBy="purchaseGroup")
     */
    protected $purchases;
	
	public function __construct()
    {
        $this->purchases = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
	
    public function getDescription()
    {
        return $this->description;
    }	
	
    public function setDescription($description)
    {
        $this->description = $description;
    }	
	
	/* Net Minus all Purchase Spend */
	public function getPurchaseSpendAmount(){
			
		$spend = 0;
		foreach($this->getPurchases() as $purchase){
			$spend = $spend + $purchase->getTotalSpend();
		}
		
		return $spend;
	}		
	
    public function getPurchases()
    {
        return $this->purchases;
    }		
	
	public function getTotalSold(){
		
		$x = 0;
		
		foreach($this->getPurchases() as $purchase){
			
			if($purchase->getStatus == \App\Models\PurchaseStatus::Sold()){
				$x++;
			}
			
		}
		
		return $x;
	
		
	}
	
	public function getTotalSpend(){
		
		$x = 0;
		
		foreach($this->getPurchases() as $purchase){
			$x += $purchase->getTotalSpend();
		}
		
		return $x;
	
		
	}	
	
	public function getTotalProfit(){
		
		$x = 0;
		
		foreach($this->getPurchases() as $purchase){
			if($purchase->getSale()){
				
			$x += $purchase->getSale()->getProfitAmount() / $purchase->getSale()->getPurchases()->count();
			}
		}
		
		return $x;		
	}
  
}