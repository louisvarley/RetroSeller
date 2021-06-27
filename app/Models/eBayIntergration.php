<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_ebay_intergrations")
 */
class eBayIntergration
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
    protected $user_id;	

	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $dev_id;	
	
	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $app_id;	
	
	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $cert_id;	

	/**
    * @ORM\Column(type="text", nullable="false")
    */
    protected $auth_token;
	
	
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
	
    public function getDevId()
    {
        return $this->dev_id;
    }

    public function setDevId($dev_id)
    {
        $this->dev_id = $dev_id;
    }
	
    public function getAppId()
    {
        return $this->app_id;
    }

    public function setAppId($app_id)
    {
        $this->app_id = $app_id;
    }	
	
    public function getCertId()
    {
        return $this->cert_id;
    }

    public function setCertId($cert_id)
    {
        $this->cert_id = $cert_id;
    }		

    public function getAuthToken()
    {
        return $this->auth_token;
    }

    public function setAuthToken($auth_token)
    {
        $this->auth_token = $auth_token;
    }	
}