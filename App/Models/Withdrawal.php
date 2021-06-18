<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_withdrawals")
*/
class Withdrawal
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue
    */
    protected $id;	
	
	/**
     * @ORM\Column(type="decimal", precision=7, scale=2)
    */
    protected $amount = 0;

	/**
    * @ORM\Column(type="string")
    */
    protected $description;
	
	/**
    * @ORM\Column(type="date")
    */
    protected $date;	
  
	/**
    * @ORM\ManyToOne(targetEntity="Account", inversedBy="withdrawals")
    * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
    */	
    protected $account;

	public function __construct() {
        $this->purchases = new ArrayCollection();
    }	

    public function getId()
    {
        return $this->id;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }	

    public function getAmount()
    {
        return $this->amount;
    }	

    public function getAccount()
    {
        return $this->account;
    }		

    public function setAccount(Account $account)
    {
        $this->account = $account;
    }	
	
    public function getDescription()
    {
        return $this->description;
    }	
	
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDate()
    {
        return $this->date;
    }	
	
    public function setDate($date)
    {
        $this->date = $date;
    }	
	
}