<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_account")
 */
class Account
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
     * Many Accounts have Many Sales.
     * @ORM\ManyToMany(targetEntity="Sale", mappedBy="accounts")
     */ 
    protected $sales;	
	
	/**
    * @ORM\OneToMany(targetEntity="Withdrawal", mappedBy="account")
    */	
    protected $withdrawals;
	
	/**
    * @ORM\OneToMany(targetEntity="Expense", mappedBy="account")
    */	
    protected $expenses;	
	
	/**
    * @ORM\OneToMany(targetEntity="Buyout", mappedBy="account")
    */	
    protected $buyout;		
	
	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $color;		
	
	public function __construct()
    {
        $this->sales = new ArrayCollection();
		$this->withdrawals = new ArrayCollection();
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
	
    public function getSales()
    {
        return $this->sales;
    }	
	
    public function getExpenses()
    {
        return $this->expenses;
    }	

    public function getWithdrawals()
    {
        return $this->withdrawals;
    }		
	
    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }		
	
	public function getMyProfit(){
		
		$balance = 0;
		
		/* Handle Sales */
		foreach($this->getSales() as $sale){
			
			if($sale->isComplete()){
				/* get profit shares */
				$balance = $balance + $sale->getProfitAmount() / $sale->getAccounts()->count();
			}
		}
		
		return $balance;
		
	}
	
	public function getBalance(){
		
		$balance = 0;
		
		/* Handle Sales */
		foreach($this->getSales() as $sale){
			
			if($sale->isComplete()){
			
				/* get profit shares */
				$balance = $balance + $sale->getProfitAmount() / $sale->getAccounts()->count();
				
				/* get expenses */
				foreach($sale->getPurchases() as $purchase){
					foreach($purchase->getExpenses() as $expense){
						
						/* Exclude expenses from bought out items */
						if($expense->getAccount()->getId() == $this->getId()){
							if($purchase->getBuyOut() == null){
								$balance = $balance + ($expense->getAmount() / $expense->getPurchases()->count());
							}
						}
					}
				}
			
			}
		}
		
		/* Handle Buyouts. */
		foreach(findAll("Purchase") as $purchase){
			
			if($purchase->getBuyOut() != null){
				foreach($purchase->getExpenses() as $expense){
					
					/* If there was a buyout made, you get your expenses paid now */
					if($expense->getAccount()->getId() == $this->getId()){
						$balance = $balance + ($expense->getAmount() / $expense->getPurchases()->count());
					}
					
					/* and the buy out account loses that amount from their balance */
					if($purchase->getBuyOut()->getAccount()->getId() == $this->getId()){
						$balance = $balance - ($expense->getAmount() / $expense->getPurchases()->count());
					}
				}		
			}
		}
		
		/* Minus all withdrawals */
		foreach($this->getWithdrawals() as $withdrawal){
			$balance = $balance - ($withdrawal->getAmount());
		}			
		
		return $balance;
		
	}
}