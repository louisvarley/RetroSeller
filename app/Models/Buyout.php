<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_buyouts")
*/
class Buyout
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue
    */
    protected $id;	

	/**
    * @ORM\OneToOne(targetEntity="Purchase", inversedBy="buyout")
    * @ORM\JoinColumn(name="purchase_id", referencedColumnName="id")
    */	
    protected $purchase;

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

    public function getPurchase()
    {
        return $this->purchase;
    }	

    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }			

	public function getDate()
    {
        return $this->date;
    }	
	
    public function setDate($date)
    {
        $this->date = $date;
    }	

    public function getAccount()
    {
        return $this->account;
    }	

    public function setAccount($account)
    {
        $this->account = $account;
    }		

}