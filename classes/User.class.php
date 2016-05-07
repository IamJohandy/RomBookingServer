<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 04.05.2016
 * Time: 13.57
 */

class User
{
    private $bruker_kode;
    private $bruker_type_id;
    private $fornavn;
    private $etternavn;
    private $epost;
    private $passord;
    private $random;
    private $verifisert;

    function __construct(){}

    public static function create()
    {
        $instance = new self();
        return $instance;
    }

    public function toArray()
    {
        $temp = array();
        $temp['username'] = $this->bruker_kode;
        $temp['email'] = $this->epost;

        return $temp;
    }

    /**
     * @return mixed
     */
    public function getUserCode()
    {
        return $this->bruker_kode;
    }

    /**
     * @param mixed $user_code
     */
    public function setUserCode($user_code)
    {
        $this->bruker_kode = $user_code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserType()
    {
        return $this->bruker_type_id;
    }

    /**
     * @param mixed $user_type_id
     */
    public function setUserType($user_type_id)
    {
        $this->bruker_type_id = $user_type_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->fornavn;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->fornavn = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->etternavn;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->etternavn = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->epost;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->epost = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->passord;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->passord = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRandom()
    {
        return $this->random;
    }

    /**
     * @param mixed $random
     */
    public function setRandom($random)
    {
        $this->random = $random;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isVerified()
    {
        return $this->verifisert;
    }

    /**
     * @param mixed $verified
     */
    public function setVerified($verified)
    {
        $this->verifisert = $verified;
        return $this;
    }
}