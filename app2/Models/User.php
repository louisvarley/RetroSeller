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

	public function validatePassword($password){
		
		if(password_verify($password, $this->password_hash)){
			return true;
		};
		return false;
	}

}