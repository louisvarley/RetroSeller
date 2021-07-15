<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_transfers")
*/
class Transfer
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
    * @ORM\ManyToOne(targetEntity="Account")
    * @ORM\JoinColumn(name="account_to_id", referencedColumnName="id")
    */	
    protected $account_to;
	
	/**
    * @ORM\ManyToOne(targetEntity="Account")
    * @ORM\JoinColumn(name="account_from_id", referencedColumnName="id")
    */	
    protected $account_from;	
	
	/**
    * @ORM\Column(type="date")
    */
    protected $date;	
	

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
	
    public function getAmount()
    {
        return $this->amount;
    }	
	
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }	

    public function getAccountTo()
    {
        return $this->account_to;
    }		

    public function setAccountTo(Account $account_to)
    {
        $this->account_to = $account_to;
    }	
		
    public function getAccountFrom()
    {
        return $this->account_from;
    }		

    public function setAccountFrom(Account $account_from)
    {
        $this->account_from = $account_from;
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