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
	* @ORM\OrderBy({"date" = "DESC"})	 
     */ 
    protected $sales;	
	
	/**
    * @ORM\OneToMany(targetEntity="Withdrawal", mappedBy="account")
	* @ORM\OrderBy({"date" = "DESC"})	
    */	
    protected $withdrawals;
	
	/**
    * @ORM\OneToMany(targetEntity="Expense", mappedBy="account")
	* @ORM\OrderBy({"date" = "DESC"})	
    */	
    protected $expenses;	
	
	/**
    * @ORM\OneToMany(targetEntity="Buyout", mappedBy="account")
	* @ORM\OrderBy({"date" = "DESC"})
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
		
		$amount = 0;
		
		foreach($this->getTransactions() as $transaction){
			
			if($transaction['type'] == 'PROFIT_PAY_OUT'){
				$amount = $amount + $transaction['amount'];
			}
			
		}
		
		return $amount;
	}
	
	public function getBalance(){
				
		$transactions = $this->getTransactions();
		return end($transactions)['balance'];		
	}

	public function getTransactions(){
		
		$transactions = [];
		
		/* For Each Sale */
		foreach($this->getSales() as $sale){
			
			/* If is Completed */
			if($sale->isComplete()){
			
				/* For Each Purchase Within the Sale */
				foreach($sale->getPurchases() as $purchase){
					
						/* For Each Expenses Within the Purchase */
						foreach($purchase->getExpenses() as $expense){
							
							/* Expense is Mine */
							if($expense->getAccount()->getId() == $this->getId()){
								
								/* Was not a buyout - Should not ever be the case */
								if($purchase->getBuyOut() == null){
									
									/* Add Expense */
									array_push($transactions, [
									'date' => $sale->getDate(),
									'type' => 'EXPENSE_PAY_OUT',
									'description' => $expense->getName(),
									'amount' => $expense->getAmount() / $expense->getPurchases()->count()
									]);
									
								}
							}
						}
				}
				
				/* Add Profit From This Sale */
				array_push($transactions, [
				'date' => $sale->getDate(),
				'type' => "PROFIT_PAY_OUT",
				'description' => $sale->getPurchasesString(),
				'amount' => $sale->getProfitAmount() / $sale->getAccounts()->count()
				]);
				
			}

		}
		
		/*  For each Buyout */
		foreach(findAll("buyout") as $buyout){

			/* For Each Purchase In Buyout */
			foreach($buyout->getPurchase()->getExpenses() as $expense){
				
				/* If Expense was yours... */
				if($expense->getAccount()->getId() == $this->getId()){
					
					/* And Buyout was not yours */
					if($buyout->getAccount()->getId() != $this->getId()){
					
						array_push($transactions, [
						'date' => $buyout->getDate(),
						'type' => "BUYOUT_PAY_OUT",
						'description' => $buyout->getPurchase()->getName() . ' buyout from ' . $buyout->getAccount()->getName(),
						'amount' => $expense->getAmount() / $expense->getPurchases()->count()
						]);
					
					}
					
				}else{
					
					/* Otherwise, you are paying the buyout */
					array_push($transactions, [
					'date' => $buyout->getDate(),
					'type' => "BUYOUT_PAY_IN",
					'description' => $buyout->getPurchase()->getName() . ' buyout paid to ' . $expense->getAccount()->getName(),
					'amount' => 0 - $expense->getAmount() / $expense->getPurchases()->count()
					]);
				}
			}
		}

		/* For each Withdrawl */
		foreach(findAll("withdrawal") as $withdrawal){
			
			/* If withdrawl is yours */
			if($withdrawal->getAccount()->getId() == $this->getId()){
				
				array_push($transactions, [
					'date' => $withdrawal->getDate(),
					'type' => "WITHDRAW",
					'description' =>$withdrawal->getDescription(),
					'amount' => 0 - $withdrawal->getAmount()
					]);
				
			}
			
		}
	
		/* Now Sort array by date */
		usort($transactions, function($a, $b){

		  $ad = ($a['date']);
		  $bd = ($b['date']);

		  if ($ad == $bd) {
			return 0;
		  }

		  return $ad < $bd ? -1 : 1;
		});
		
		/* Default */
		if(count($transactions) == 0){
			
			array_push($transactions, [
					'date' => new \DateTime(),
					'type' => "OPENING",
					'description' =>"Opening Balance",
					'amount' => 0
					]);
		}
		
		/* Add Balance */
		$balance = 0;
		foreach($transactions as $key => $transaction){
			
			$balance = $balance + $transaction['amount'];
			$transactions[$key]['balance'] = $balance;
	
		}
		
		
		
		return $transactions;
		
	}

}