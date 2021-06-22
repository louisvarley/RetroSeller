<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_sales")
 */
class Sale
{
	/**
    * @ORM\Id
    * @ORM\Column(type="integer", name="id")
    * @ORM\GeneratedValue
    */
    protected $id;	
	
	/**
    * @ORM\ManyToOne(targetEntity="SaleVendor")
    * @ORM\JoinColumn(name="sale_vendor_id", referencedColumnName="id")
    */	
    protected $sale_vendor;	

	/**
    * @ORM\ManyToOne(targetEntity="PaymentVendor")
    * @ORM\JoinColumn(name="payment_vendor_id", referencedColumnName="id")
    */	
    protected $payment_vendor;	
	 
    // ...
    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Purchase", mappedBy="sale")
     */
    protected $purchases;
	
	/**
    * @ORM\Column(type="date")
    */
    protected $date;	
	
	/**
     * @ORM\Column(type="decimal", precision=7, scale=2)
    */
    protected $gross_amount = 0;	

	/**
     * @ORM\Column(type="decimal", precision=7, scale=2)
    */
    protected $postage_cost = 0;
	
	/**
     * @ORM\Column(type="decimal", precision=7, scale=2)
    */
    protected $fee_cost = 0;	
	
	/**
    * @ORM\Column(type="integer", nullable="true")
    */
    protected $ebay_item_no;	
	
    /**
     * Many Sales have Many Accounts.
     * @ORM\ManyToMany(targetEntity="Account", inversedBy="sales")
     * @ORM\JoinTable(name="rs_sale_accounts")	 
     */	 
    protected $accounts;

	public function __construct()
    {
        $this->purchases = new ArrayCollection();
        $this->accounts = new ArrayCollection();		
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEbayItemNo()
    {
        return $this->ebay_item_no;
    }

    public function setEbayItemNo($ebay_item_no)
    {
        $this->ebay_item_no = $ebay_item_no;
    }
	
    public function getGrossAmount()
    {
        return $this->gross_amount;
    }	
	
    public function setGrossAmount($gross_amount)
    {
        $this->gross_amount = $gross_amount;
    }	
	
    public function getPostageCost()
    {
        return $this->postage_cost;
    }	
	
    public function setPostageCost($postage_cost)
    {
        $this->postage_cost = $postage_cost;
    }		
	
    public function getFeeCost()
    {
        return $this->fee_cost;
    }	
	
    public function setFeeCost($fee_cost)
    {
        $this->fee_cost = $fee_cost;
    }		

    public function getPurchases()
    {
        return $this->purchases;
    }	
	
    public function getAccounts()
    {
        return $this->accounts;
    }		
	
    public function getSaleVendor()
    {
        return $this->sale_vendor;
    }	
	
    public function setSaleVendor($sale_vendor)
    {
        $this->sale_vendor = $sale_vendor;
    }		

    public function getPaymentVendor()
    {
        return $this->payment_vendor;
    }	
	
    public function setPaymentVendor($payment_vendor)
    {
        $this->payment_vendor = $payment_vendor;
    }			
	
    public function getDate()
    {
        return $this->date;
    }	
	
    public function setDate($date)
    {
        $this->date = $date;
    }		

	/* Gross Minus Fees Minus Postage Cost */
	public function getNetAmount(){
		
		return $this->getGrossAmount() - ($this->getFeeCost() + $this->getPostageCost());
	}
	
	/* Net Minus all Purchase Spend */
	public function getPurchaseSpendAmount(){
			
		$spend = 0;
		foreach($this->getPurchases() as $purchase){
			$spend = $spend + $purchase->getTotalSpend();
		}
		
		return $spend;
	}		
	
	/* Net Minus all Purchase Spend */
	public function getProfitAmount(){
		return $this->getNetAmount() - $this->getPurchaseSpendAmount();
	}	
	
	public function getPurchasesString(){
			
		$nameArr = [];
		foreach($this->getPurchases() as $purchase){
			array_push($nameArr, $purchase->getName());
		}
		
		return implode(",",$nameArr);
	}
	
}