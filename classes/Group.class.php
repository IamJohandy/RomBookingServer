<?php

/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2016-03-26
 * Time: 23:20
 */
class Group
{

    private $groupId;
    private $member1;
    private $member2;
    private $member3;
    private $member4;
    private $member5;

    function __construct($id)
    {
        $this->groupId = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->groupId;
    }

    /**
     * @param mixed $groupId
     */
    public function setId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return mixed
     */
    public function getUser1()
    {
        return $this->member1;
    }

    /**
     * @return mixed
     */
    public function getUser2()
    {
        return $this->member2;
    }

    /**
     * @return mixed
     */
    public function getUser3()
    {
        return $this->member3;
    }

    /**
     * @return mixed
     */
    public function getUser4()
    {
        return $this->member4;
    }

    /**
     * @return mixed
     */
    public function getUser5()
    {
        return $this->member5;
    }

    public function addUser($userId)
    {
        if ($this->member1 == null) {
            $this->member1 = $userId;
        } else if ($this->member2 == null) {
            $this->member2 = $userId;
        } else if ($this->member3 == null) {
            $this->member3 = $userId;
        } else if ($this->member4 == null) {
            $this->member4 = $userId;
        } else if ($this->member5 == null) {
            $this->member5 = $userId;
        } else {
            return false;
        }

        return true;
    }
}