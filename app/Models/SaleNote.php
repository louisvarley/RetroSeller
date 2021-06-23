<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_sale_notes")
 */
class SaleNote
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
    * @ORM\ManyToOne(targetEntity="Sale", inversedBy="saleNotes")
    * @ORM\JoinColumn(name="sale_id", referencedColumnName="id")
    */	
    protected $sale;	

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
	
	public function getSale()
	{
		return $this->sale;
	}
	
	public function setSale($sale)
	{
		$this->sale = $sale;
	}
	
	public function getUser()
	{
		return $this->user;
	}
	
	public function setUser()
	{
		$this->user = authenticationManager()->me();
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