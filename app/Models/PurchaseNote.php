<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_purchase_notes")
 */
class PurchaseNote
{
	/**
    * @ORM\Id
    * @ORM\Column(type="integer", name="id")
    * @ORM\GeneratedValue
    */
    protected $id;	
	
	/**
    * @ORM\Column(type="date")
    */
    protected $date;	
		
	/**
    * @ORM\ManyToOne(targetEntity="Purchase", inversedBy="purchaseNotes")
    * @ORM\JoinColumn(name="purchase_id", referencedColumnName="id")
    */	
    protected $purchase;	

	/**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    */	
    protected $user;
	
	/**
    * @ORM\Column(type="string")
    */
    protected $note;	
	
	
    public function getId()
    {
        return $this->id;
    }

    public function getDate()
	{
		return $this->date;
	}
	
	public function setDate($date)
	{
		$this->date = $date;
	}
	
	public function getPurchase()
	{
		return $this->purchase;
	}
	
	public function setPurchase($purchase)
	{
		$this->purchase = $purchase;
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function setUser()
	{
		$this->user = Authentication::me();
	}
   
	public function getNote()
	{
		return $this->note;
	}
	
	public function setNote($note)
	{
		$this->note = $note;
	}	   
	
}