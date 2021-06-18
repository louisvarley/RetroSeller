<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_expenses")
*/
class Expense
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
     * @ORM\Column(type="decimal", precision=7, scale=2)
    */
    protected $amount = 0;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="Purchase", inversedBy="expenses")
     * @ORM\JoinTable(name="rs_purchase_expenses")	 
     */	 
    protected $purchases;

	/**
    * @ORM\ManyToOne(targetEntity="Account")
    * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
    */	
    protected $account;
	
	/**
    * @ORM\Column(type="date")
    */
    protected $date;	

	public function __construct() {
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
	
    public function getPurchases()
    {
        return $this->purchases;
    }		

    public function getAmount()
    {
        return $this->amount;
    }	
	
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }	

    public function getAccount()
    {
        return $this->account;
    }		

    public function setAccount(Account $account)
    {
        $this->account = $account;
    }	
	
	
	public function getDate()
    {
        return $this->date;
    }	
	
    public function setDate($date)
    {
        $this->date = $date;
    }	
	public function getProporationAmount(){
		
		return $this->getAmount() / $this->getPurchases()->count();
		
	}
	
	

	
}