<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_addresses")
 */
class Address
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
    protected $line_1;	
	
	/**
    * @ORM\Column(type="string", nullable="true")
    */
    protected $line_2;

	/**
    * @ORM\Column(type="string", nullable="true")
    */
    protected $line_3;

	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $city;

	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $state;

	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $postal_code;	
	
	
    public function getId()
    {
        return $this->id;
    }

    public function getLine1()
    {
        return $this->line_1;
    }

    public function setLine1($line1)
    {
        $this->line_1 = $line1;
    }
	
    public function getLine2()
    {
        return $this->line_2;
    }

    public function setLine2($line2)
    {
        $this->line_2 = $line2;
    }  
	
    public function getLine3()
    {
        return $this->line_3;
    }

    public function setLine3($line3)
    {
        $this->line_3 = $line3;
    }  	
	
    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }  	

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    } 
	
    public function getPostalCode()
    {
        return $this->postal_code;
    }

    public function setPostalCode($postalCode)
    {
        $this->postal_code = $postalCode;
    } 	
	
	public function asArray(){
		
		$address = [];
		
		if($this->getLine1()) $address[] = $this->getLine1(); 
		if($this->getLine2()) $address[] = $this->getLine2(); 
		if($this->getLine3()) $address[] = $this->getLine3(); 		
		if($this->getCity()) $address[] = $this->getCity(); 	
		if($this->getState()) $address[] = $this->getState(); 	
		if($this->getPostalCode()) $address[] = $this->getPostalCode(); 		

		return $address;
		
	}

}