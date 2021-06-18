<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_invoices")
 */
class Invoice
{
	/**
    * @ORM\Id
    * @ORM\Column(type="integer", name="id")
    * @ORM\GeneratedValue
    */
    protected $id;	
	
    // ...
    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="purchase", mappedBy="sale")
     */
    protected $purchases;
	
	/**
    * @ORM\Column(type="date")
    */
    protected $date;	
	
	/**
     * @ORM\Column(type="decimal", precision=7, scale=2)
    */
    protected $postage_charge = 0;


	public function __construct()
    {
        $this->purchases = new ArrayCollection();
        
    }

    public function getId()
    {
        return $this->id;
    }

   

    public function getPurchases()
    {
        return $this->purchases;
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