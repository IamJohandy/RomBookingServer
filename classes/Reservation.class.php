<?php

/**
 * Reservation class representing as the name suggest a room reservation.
 *
 * Reservation object class representing reservation class used to fetch reservation objects from database, as well
 * as sending new reservations to the database.
 *
 * @since 1.0.0
 */
class Reservation
{
    /**
     * int containing reservation id in database.
     *
     * @since 1.0.0
     * @access private
     * @var int $reservasjons_id Database internal reservation id.
     */
    private $reservasjons_id;

    /**
     * String representing reservations start date in "YYYY-MM-DD HH:MM:SS" format.
     *
     * @since 1.0.0
     * @access private
     * @var string $fra Reservations start date, "YYYY-MM-DD HH:MM:SS".
     */
    private $fra;

    /**
     * String representing reservations end date in "YYYY-MM-DD HH:MM:SS" format.
     *
     * @since 1.0.0
     * @access private
     * @var string $til Reservations end date, "YYYY-MM-DD HH:MM:SS".
     */
    private $til;

    /**
     * String specifying reservations room number.
     *
     * @since 1.0.0
     * @access private
     * @var string $roo_kode Reserved room.
     */
    private $rom_kode;

    /**
     * String representing group that filed the reservation.
     *
     * @since 1.0.0
     * @access private
     * @var string $gruppe_kode Groups id.
     */
    private $gruppe_kode;

    /**
     * String representing group leader id, later on his permissions will affect groups permission.
     *
     * @since 1.0.0
     * @access private
     * @var string $gruppe_leder Group leader id.
     */
    private $gruppe_leder;

    /**
     * String containing reservations purpose.
     *
     * @since 1.0.0
     * @access private
     * @var string $formal Reservations purpose.
     */
    private $formal;

    /**
     * Creates new instance of Reservation class.
     *
     * Used to create new Reservation objects, mostly used when fetching reservations from database.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct()
    {
    }

    /**
     * Static constructor returns empty Reservation object, used with fluent setters
     *
     * Used to create new Reservation object, mostly used with fluent setters to make a new reservation object with
     * data provided by user.
     *
     * @since 1.0.0
     * @access public
     *
     * @return Reservation new and empty reservation object.
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Returns reservations id.
     *
     * Returns database internal reservation id.
     *
     * @since 1.0.0
     * @access public
     *
     * @global int $reservasjons_id Represents database internal reservation id.
     *
     * @return int reservations id.
     */
    public function getId()
    {
        return $this->reservasjons_id;
    }

    /**
     * Returns reservations start date.
     *
     * Returns reservations start date in "YYYY-MM-DD HH:MM:SS" format.
     *
     * @since 1.0.0
     * @access public
     *
     * @global string $fra Represents reservations start date in "YYYY-MM-DD HH:MM:SS" format.
     *
     * @return string reservations start date, "YYYY-MM-DD HH:MM:SS".
     */
    public function getFrom()
    {
        return $this->fra;
    }

    /**
     * Set reservations start date.
     *
     * Set reservations start date in "YYYY-MM-DD HH:MM:SS" format.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $from reservations start date, "YYYY-MM-DD HH:MM:SS".
     * @return Reservation object used in fluent object creation.
     */
    public function setFrom($from)
    {
        $this->fra = $from;
        return $this;
    }

    /**
     * Returns reservations end date.
     *
     * Returns reservations start end in "YYYY-MM-DD HH:MM:SS" format.
     *
     * @since 1.0.0
     * @access public
     *
     * @global string $fra Represents reservations start end in "YYYY-MM-DD HH:MM:SS" format.
     *
     * @return string reservations end date, "YYYY-MM-DD HH:MM:SS".
     */
    public function getTo()
    {
        return $this->til;
    }

    /**
     * Set reservations end date.
     *
     * Set reservations end date in "YYYY-MM-DD HH:MM:SS" format.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $to reservations start date, "YYYY-MM-DD HH:MM:SS".
     * @return Reservation object used in fluent object creation.
     */
    public function setTo($to)
    {
        $this->til = $to;
        return $this;
    }

    /**
     * Returns reservations room code.
     *
     * Returns room code that this reservation is booking.
     *
     * @since 1.0.0
     * @access public
     *
     * @global string $rom_kode Specifies reservations room number.
     *
     * @return string reservations room code.
     */
    public function getRoom()
    {
        return $this->rom_kode;
    }

    /**
     * Set reservations room.
     *
     * Set reservations room id.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $room reservations room.
     * @return Reservation object used in fluent object creation.
     */
    public function setRoom($room)
    {
        $this->rom_kode = $room;
        return $this;
    }

    /**
     * Returns reservations group code.
     *
     * Returns group code that filed this reservation.
     *
     * @since 1.0.0
     * @access public
     *
     * @global string $gruppe_kode group id.
     *
     * @return string representing group that filed the reservation.
     */
    public function getGroup()
    {
        return $this->gruppe_kode;
    }

    /**
     * Set reservations group.
     *
     * Set reservations group id.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $group reservations group.
     * @return Reservation object used in fluent object creation.
     */
    public function setGroup($group)
    {
        $this->gruppe_kode = $group;
        return $this;
    }

    /**
     * Returns group leader id.
     *
     * Returns group leader id whom have created the group.
     *
     * @since 1.0.0
     * @access public
     *
     * @global string $gruppe_leder representing group leader id.
     *
     * @return string representing group leader id.
     */
    public function getLeader()
    {
        return $this->gruppe_leder;
    }

    /**
     * Set reservations leader.
     *
     * Set reservations leader id.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $leader reservations leader.
     * @return Reservation object used in fluent object creation.
     */
    public function setLeader($leader)
    {
        $this->gruppe_leder = $leader;
        return $this;
    }

    /**
     * Returns reservations purpose.
     *
     * Returns reservations purpose explaining why this reservation was filed.
     *
     * @since 1.0.0
     * @access public
     *
     * @global string $formal Reservations purpose.
     *
     * @return string representing reservations purpose.
     */
    public function getPurpose()
    {
        return $this->formal;
    }

    /**
     * Set reservations purpose.
     *
     * Set reservations purpose.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $purpose reservations purpose.
     * @return Reservation object used in fluent object creation.
     */
    public function setPurpose($purpose)
    {
        $this->formal = $purpose;
        return $this;
    }

    public function getDate()
    {
        $return = '';
        $temp1 = preg_split("/ /", $this->fra);
        $return = $temp1[0] . ' ';
        $temp2 = explode(':', $temp1[1]);
        $return .= $temp2[0] . ':';
        $return .= $temp2[1] . '-';
        $temp1 = preg_split("/ /", $this->til);
        $temp2 = explode(':', $temp1[1]);
        $return .= $temp2[0] . ':';
        $return .= $temp2[1];

        return $return;
    }
}