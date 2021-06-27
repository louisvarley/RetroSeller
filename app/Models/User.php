<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="rs_users")
 */
class User
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
    protected $email;	
	
	/**
    * @ORM\Column(type="string", nullable="false")
    */
    protected $apikey;	
	
	/**
    * @ORM\Column(type="string", nullable="false")
    */
    private $password_hash;		
	
	
    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
	
    public function setPassword($password)
    {
		$this->password_hash = \password_hash ($password,   PASSWORD_DEFAULT  );
    }
	
    public function getApiKey()
    {
        return $this->apikey;
    }

    public function generateApiKey()
    {
        $this->apikey = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));
    }	

	public function validatePassword($password){
		
		if(password_verify($password, $this->password_hash)){
			return true;
		};
		return false;
	}

}