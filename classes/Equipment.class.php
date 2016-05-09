<?php

/**
 * Created by PhpStorm.
 * User: ChrissyBoy
 * Date: 2016-04-05
 * Time: 14:27
 */
class Equipment
{
    private $utstyr_kode;
    private $utstyr_navn;


    function __construct()
    {
    }

    public static function create()
    {
        $instance = new self();
        return $instance;
    }

    /**
     * @return mixed
     */
    public function getEquipmentCode()
    {
        return $this->utstyr_kode;
    }

    /**
     * @param mixed $equipment_code
     */
    public function setEquipmentCode($equipment_code)
    {
        $this->utstyr_kode = $equipment_code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEquipmentName()
    {
        return $this->utstyr_navn;
    }

    /**
     * @param mixed $equipment_code
     */
    public function setEquipmentName($equipment_name)
    {
        $this->utstyr_navn = $equipment_name;
        return $this;
    }

}