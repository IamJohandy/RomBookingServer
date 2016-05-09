<?php

/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 2016-03-11
 * Time: 12:40
 */
class Room
{
    private $rom_kode;
    private $rom_navn;
    private $rom_type_kode;
    private $kapasitet_eks;
    private $kapasitet_und;
    private $campus_id;
    private $er_aktiv;

    function __construct()
    {
    }

    public static function create()
    {
        $instance = new self();
        return $instance;
    }

    public function toArray()
    {
        $temp = array();
        $temp['roomCode'] = $this->rom_kode;
        $temp['roomName'] = $this->rom_navn;
        $temp['roomType'] = $this->rom_type_kode;
        $temp['capEx'] = $this->kapasitet_eks;
        $temp['capLect'] = $this->kapasitet_und;
        $temp['campus'] = $this->campus_id;
        $temp['active'] = $this->er_aktiv;

        return $temp;
    }

    /**
     * @return mixed
     */
    public function getRoomCode()
    {
        return $this->rom_kode;
    }

    /**
     * @param mixed $room_code
     * @return Rom
     */
    public function setRoomCode($room_code)
    {
        $this->rom_kode = $room_code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoomName()
    {
        return $this->rom_navn;
    }

    /**
     * @param mixed $room_name
     * @return Rom
     */
    public function setRoomName($room_name)
    {
        $this->rom_navn = $room_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus_id;
    }

    /**
     * @param mixed $room_name
     * @return Rom
     */
    public function setCampus($name)
    {
        $this->campus_id = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoomTypeCode()
    {
        return $this->rom_type_kode;
    }

    /**
     * @param mixed $room_type_code
     * @return Room
     */
    public function setRoomTypeCode($room_type_code)
    {
        $this->rom_type_kode = $room_type_code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCapacityEx()
    {
        return $this->kapasitet_eks;
    }

    /**
     * @param mixed $capacityEx
     * @return Room
     */
    public function setCapacityEx($capacityEx)
    {
        $this->kapasitet_eks = $capacityEx;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCapacityLect()
    {
        return $this->kapasitet_und;
    }

    /**
     * @param mixed $capacityLecture
     * @return Room
     */
    public function setCapacityLecture($capacityLecture)
    {
        $this->kapasitet_und = $capacityLecture;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->er_aktiv;
    }

    /**
     * @param boolean $active
     * @return Room
     */
    public function setActive($active)
    {
        $this->er_aktiv = $active;
        return $this;
    }
}