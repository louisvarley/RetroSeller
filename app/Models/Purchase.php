<?php
namespace App\Models;

use \App\Models\PurchaseVendor;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_purchases")
 */
class Purchase
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
    * @ORM\ManyToOne(targetEntity="PurchaseVendor")
    * @ORM\JoinColumn(name="purchase_vendor_id", referencedColumnName="id")
    */	
    protected $purchase_vendor;	
	
	/**
    * @ORM\ManyToOne(targetEntity="PurchaseCategory")
    * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
    */	
    protected $category;		

	/**
    * @ORM\ManyToOne(targetEntity="PurchaseStatus")
    * @ORM\JoinColumn(name="purchase_status_id", referencedColumnName="id")
    */	
    protected $status;
	

	
    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Sale", inversedBy="purchases")
     * @ORM\JoinColumn(name="sale_id", referencedColumnName="id")
     */
    protected $sale;	
	
	/**
     * @ORM\Column(type="decimal", precision=7, scale=2)
    */
    protected $valuation = 0;		
	
	/**
    * @ORM\Column(type="date")
    */
    protected $date;

    /**
     * Many Purchases have Many Expenses.
     * @ORM\ManyToMany(targetEntity="Expense", mappedBy="purchases")
     */ 
    protected $expenses;

	/**
    * @ORM\OneToOne(targetEntity="Buyout", mappedBy="purchase")
    */	
    protected $buyout;	

	public function __construct()
    {
        $this->expenses = new ArrayCollection();		
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
	
    public function getPurchaseVendor()
    {
        return $this->purchase_vendor;
    }	
	
    public function setPurchaseVendor($purchase_vendor)
    {
        $this->purchase_vendor = $purchase_vendor;
    }		
	
    public function getBuyout()
    {
		return $this->buyout;
    }		
	
    public function getStatus()
    {
		return $this->status;
    }
	
    public function setStatus($status)
    {
        $this->status = $status;
    }		
	
    public function getDate()
    {
        return $this->date;
    }	
	
    public function setDate($date)
    {
        $this->date = $date;
    }		

    public function getCategory()
    {
        return $this->category;
    }	
	
    public function setCategory($category)
    {
        $this->category = $category;
    }		


	public function getExpenses()
	{
		return $this->expenses;
		
	}
	
    public function setSale($sale)
    {
        $this->sale = $sale;
    }		


	public function getValuation()
	{
		return $this->valuation;
		
	}
	
    public function setValuation($valuation)
    {
        $this->valuation = $valuation;
    }	

	public function getSale()
	{
		return $this->sale;
	}	
	
	public function getTotalSpend()
	{
		
		$total = 0;

		
		foreach($this->getExpenses() as $expense)
		{		
			$total = $total + ($expense->getAmount() / count($expense->getPurchases()));
		}
		
		return $total;
		
	}	
	
	public function getExpenseShare(){
		
		$total = $this->getTotalSpend();
		
		$accounts = [];
		
		foreach(findAll("Account") as $account){
			
			$accounts[$account->getId()] = [
			'id' => $account->getId(),
			'name' => $account->getName(),
			'amount' => 0,
			'color' => $account->getColor(),
			];
		}
		
		foreach($this->getExpenses() as $expense)
		{
			/* Buy out overrides this */
			if($this->getBuyout() != null){
				$accounts[$this->getBuyOut()->getAccount()->getId()]['amount'] += ($expense->getAmount() / count($expense->getPurchases())); 
			}else{
				$accounts[$expense->getAccount()->getId()]['amount'] += ($expense->getAmount() / count($expense->getPurchases())); 
			}
			
			
		}	
		
		foreach($accounts as $key => $account){
			if($accounts[$key]['amount'] > 0){
				$accounts[$key]['percentage'] = $accounts[$key]['amount'] / ($this->getTotalSpend() / 100);
			}else{
				$accounts[$key]['percentage'] = 0;
			}
			
		}			
		
		
		return $accounts;
		
	}
	
	
	
}