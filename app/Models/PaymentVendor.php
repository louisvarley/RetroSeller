<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_payment_vendors")
 */
class PaymentVendor
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
     * @ORM\Column(type="decimal", precision=7, scale=2)
    */
    protected $fixed_fee = 0;	

	/**
     * @ORM\Column(type="decimal", precision=7, scale=2)
    */
    protected $percentage_fee = 0;

	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $color;		
	
	
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setname($name)
    {
        $this->name = $name;
    }
	
	public function getFixedFee()
    {
        return $this->fixed_fee;
    }

    public function setFixedFee($fixed_fee)
    {
        $this->fixed_fee = $fixed_fee;
    }	
	
    public function getPercentageFee()
    {
        return $this->percentage_fee;
    }

    public function setPercentageFee($percentage_fee)
    {
        $this->percentage_fee = $percentage_fee;
    }	

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }	

	public function calculateFee($amount){
	
		return (($this->getPercentageFee() / 100) * $amount) + $this->getFixedFee();	
	}	
	
}