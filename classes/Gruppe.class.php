<?php

/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2016-03-26
 * Time: 23:20
 */
class Gruppe
{

    private $gruppe_kode;
    private $bruker_kode;

    /**
     * @return mixed
     */
    public function getGroupCode()
    {
        return $this->gruppe_kode;
    }

    /**
     * @param mixed $group_code
     */
    public function setGroupCode($group_code)
    {
        $this->gruppe_kode = $group_code;
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
    }
}