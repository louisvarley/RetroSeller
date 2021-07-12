<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_integrations")
 */
class Integration
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
    * @ORM\Column(type="string", nullable="false")
    */
    protected $ru_name;	

	/**
    * @ORM\Column(type="text", nullable="false")
    */
    protected $auth_token;
	
	/**
    * @ORM\Column(type="text", nullable="false")
    */
    protected $refresh_token;	
	
	/**
    * @ORM\Column(type="text", nullable="false")
    */
    protected $access_token;	
	
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

    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    public function setRefreshToken($refresh_token)
    {
        $this->refresh_token = $refresh_token;
    }	
	
    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }	
	
	public function getRuName(){
		
		return $this->ru_name;
		
	}
	
	public function setRuName($ru_name){
		
		$this->ru_name = $ru_name;
		
	}
	
	public function ebay(){
		
		return ebayService($this->getId());
		
	}
	
	public function getStatus(){
		
		
		if($this->getAccessToken() == null){
			return "Un-Authenticated";
			
		}
		
		$response = $this->eBay()->refreshToken();	
		
		if($response->getStatusCode() !== 200){
			return "Un-Authenticated";
		}else{
			return "Active";
		}
		
	}
	
	public function refreshToken(){
		
		$response = $this->eBay()->refreshToken();
		
		if($response->getStatusCode() !== 200){
			
			return $response;
			
		} else {
			
			$this->setAccessToken($response->access_token);
						
			entityService()->persist($this);
			entityService()->flush();
			
			return $response;
			
		}
		
	}

	public function requestAccessToken($code){

		$response = $this->eBay()->getUserToken($code);

		if($response->getStatusCode() !== 200){
			
			return $response;
			
		} else {
			
			$this->setRefreshToken($response->refresh_token);
			$this->setAccessToken($response->access_token);
						
			entityService()->persist($this);
			entityService()->flush();
			
			return $response;
			
		}

	}		
}