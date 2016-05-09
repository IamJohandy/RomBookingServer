<?php

class Helper
{
    /**
     * PDO object connected to database.
     *
     * @since 1.0.0
     * @access private
     * @var PDO $db PDO object connected to database.
     */
    private $db;

    private $site = "https://kark.hin.no/~530203/room/";

    /**
     * Creates new instance of Helper class, with connection to database.
     *
     * Takes provided PDO object that is connected to database and it creates new instance of Helper class.
     *
     * @since 1.0.0
     * @access public
     *
     * @param PDO $db PDO object that is connected to database.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Checks whether provided room code exists in database.
     *
     * Takes provided room code in string format and checks with database whether it exists or not.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $room Room code represented as string.
     * @return boolean TRUE if ok, FALSE if room code is wrong.
     */
    public function roomOk($room)
    {

        $stmt = $this->db->prepare("SELECT * FROM rom WHERE rom_kode = :room");
        $stmt->bindParam(':room', $room, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()['rom_kode'];
    }

    /**
     * Checks whether provided reservation start and stop dates are correct.
     *
     * Takes provided start and stop date in string format and checks whether start and stop are not before current
     * time, if start time is before end, if reservation length is longer than 30 minutes, whether reservation times
     * are multiple of five minutes and if start and stop dates are on the same day.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $from Reservation start date represented in this format "YYYY-MM-DD HH:MM:SS".
     * @param string $to Reservation end date represented in this format "YYYY-MM-DD HH:MM:SS".
     * @return boolean TRUE if ok, FALSE if one of dates is wrong.
     */
    public function timeOk($from, $to)
    {
        $datetimeFrom = DateTime::createFromFormat("Y-m-d H:i:s", $from);
        $datetimeTo = DateTime::createFromFormat("Y-m-d H:i:s", $to);
        if ($datetimeTo && $datetimeFrom) {
            $timestampFrom = $datetimeFrom->getTimestamp();
            $timestampTo = $datetimeTo->getTimestamp();
            $diff = ($datetimeFrom->format('Y-m-d') != $datetimeTo->format('Y-m-d'));
        } else {
            $timestampFrom = 0;
            $timestampTo = 0;
            $diff = 0;
        }
        $timestampNow = time();

        return !($timestampNow > $timestampFrom + 5 || $timestampNow > $timestampTo + 5 ||
            $timestampFrom + 1800 > $timestampTo || $timestampFrom % 300 != 0 ||
            $timestampTo % 300 != 0 || $diff);
    }


    public function getTimeDiff($from, $now)
    {
        $datetimeFrom = DateTime::createFromFormat("Y-m-d H:i:s", $from);
        $datetimeNow = DateTime::createFromFormat("Y-m-d H:i:s", $now);

        return $datetimeNow->diff($datetimeFrom, true)->days;
    }

    /**
     * Checks whether provided group id exists in database.
     *
     * Takes provided group id in string format and checks with database whether it exists or not.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $group Group id represented as string.
     * @return boolean TRUE if ok, FALSE if group code is wrong.
     */
    public function groupOk($group)
    {
        $stmt = $this->db->prepare("SELECT * FROM grupper WHERE gruppe_kode = :group");
        $stmt->bindParam(':group', $group, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch()['gruppe_kode'];
    }

    /**
     * Fetch group name from database, XSS safe.
     *
     * Takes provided group id in string format and checks with database whether it exists or not.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $group Group id represented as string.
     * @return string Group name if it exists, XSS safe.
     */
    public function getGroupName($group)
    {
        $stmt = $this->db->prepare("SELECT gruppenavn FROM gruppenavn WHERE gruppe_kode = :group");
        $stmt->bindParam(':group', $group, PDO::PARAM_INT);
        $stmt->execute();

        return htmlentities($stmt->fetch()['gruppenavn']);
    }

    /**
     * Checks whether provided leader is a member of given group.
     *
     * Takes provided group id and leader id in string format and checks with database whether he is a member of this
     * group.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $group Group id represented as string.
     * @param string $leader Leader id represented as string.
     * @return boolean TRUE if ok, FALSE if group code is wrong.
     */
    public function leaderOk($group, $leader)
    {
        $stmt = $this->db->prepare("SELECT * FROM grupper WHERE gruppe_kode = :group AND bruker_kode = :leader");
        $stmt->bindParam(':group', $group, PDO::PARAM_INT);
        $stmt->bindParam(':leader', $leader, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch()['gruppe_kode'];
    }

    /**
     * Fetch leader name from database, XSS safe.
     *
     * Takes provided leader id in string format and checks with database whether he exists or not.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $leader Leader id represented as string.
     * @return string Leaders full name if he exists, XSS safe.
     */
    public function getLeaderName($leader)
    {
        $stmt = $this->db->prepare("SELECT fornavn,etternavn FROM brukere WHERE bruker_kode = :leader");
        $stmt->bindParam(':leader', $leader, PDO::PARAM_STR);
        $stmt->execute();
        $arr = $stmt->fetch(PDO::FETCH_ASSOC);

        return htmlentities($arr['fornavn'] . ' ' . $arr['etternavn']);
    }

    /**
     * Checks whether provided purpose is a null or empty.
     *
     * Takes provided purpose in string format and checks whether it's empty or a null object.
     *
     * @since 1.0.0
     * @access public
     *
     * @see empty
     *
     * @param string $purpose Reservation purpose as string.
     * @return boolean TRUE if ok, FALSE if purpose is null or empty.
     */
    public function purposeOk($purpose)
    {
        return !empty($purpose);
    }

    /**
     * Checks if given room is occupied in given time interval.
     *
     * Takes provided room id, start and end date and checks with other reservations in database whether they collide
     * with new one. If this room is booked then it returns true, false if it's available.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $room Room id represented as string.
     * @param string $from Reservation start date represented in this format "YYYY-MM-DD HH:MM:SS".
     * @param string $to Reservation end date represented in this format "YYYY-MM-DD HH:MM:SS".
     * @return boolean TRUE if room is already booked at the given time, FALSE if room is available.
     */
    public function isBooked($room, $from, $to, $id)
    {
        if (!$this->timeOk($from, $to))
            return true;

        $stmt = $this->db->prepare("SELECT * FROM reservasjoner WHERE rom_kode = :room AND DATE(fra) = DATE(:from)");
        $stmt->bindParam(':room', $room, PDO::PARAM_STR);
        $stmt->bindParam(':from', $from, PDO::PARAM_STR);
        $stmt->execute();

        while ($reservation = $stmt->fetchObject('Reservation')) {
            $stampRFrom = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->getFrom());
            $stampRTo = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->getTo());
            $stampFrom = DateTime::createFromFormat("Y-m-d H:i:s", $from);
            $stampTo = DateTime::createFromFormat("Y-m-d H:i:s", $to);
            if ((($stampFrom > $stampRFrom && $stampFrom < $stampRTo) ||
                    ($stampTo > $stampRFrom && $stampTo < $stampRTo) ||
                    ($stampFrom == $stampRFrom && $stampTo == $stampRTo) ||
                    ($stampFrom <= $stampRFrom && $stampTo >= $stampRTo)) && $id != $reservation->getId()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fetches all available rooms in given time interval, empty if none or time is incorrect.
     *
     * Takes provided start and end date and checks first if they are correct. Then it checks with other reservations
     * in database whether they collide with provided time interval. If there are none or if the time interval is
     * incorrect an empty array is returned, else it returns array with room objects.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $from Reservation start date represented in this format "YYYY-MM-DD HH:MM:SS".
     * @param string $to Reservation end date represented in this format "YYYY-MM-DD HH:MM:SS".
     * @return array empty error, no available rooms or incorrect time, else an array with rooms indexed by room code.
     */
    public function getAvailableRooms($from, $to, $user)
    {
        if (!$this->timeOk($from, $to))
            return array();

        $rooms = $this->getAllRooms($user);

        $stmt = $this->db->prepare("SELECT * FROM reservasjoner WHERE DATE(fra) = DATE(:from)");
        $stmt->bindParam(':from', $from, PDO::PARAM_STR);
        $stmt->execute();

        while ($reservation = $stmt->fetchObject('Reservation')) {
            $stampRFrom = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->getFrom());
            $stampRTo = DateTime::createFromFormat("Y-m-d H:i:s", $reservation->getTo());
            $stampFrom = DateTime::createFromFormat("Y-m-d H:i:s", $from);
            $stampTo = DateTime::createFromFormat("Y-m-d H:i:s", $to);
            if ((($stampFrom > $stampRFrom && $stampFrom < $stampRTo) || ($stampTo > $stampRFrom && $stampTo <
                    $stampRTo) || ($stampFrom == $stampRFrom && $stampTo == $stampRTo) || ($stampFrom <= $stampRFrom
                    && $stampTo >= $stampRTo))
            )
                unset($rooms[$reservation->getRoom()]);
        }

        return $rooms;
    }

    /**
     * Fetches all available rooms.
     *
     * Fetches all available rooms in database and returns them as room objects in an array. If an error occurs an
     * empty array is returned.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @return array empty if error or no available rooms, else an array with rooms indexed by room code.
     */
    public function getAllRooms($user)
    {
        $rooms = array();

        if ($user != null && $user->getUserType() < 4) {
            $stmt = $this->db->prepare("SELECT * FROM rom WHERE rom_type_kode = 'KOL'");
        } else {
            $stmt = $this->db->prepare("SELECT * FROM rom");
        }
        $stmt->execute();
        while ($room = $stmt->fetchObject('Room'))
            $rooms[$room->getRoomCode()] = $room;

        return $rooms;
    }

    public function reservationIdOk($id, $user)
    {
        $stmt = $this->db->prepare("SELECT * FROM reservasjoner WHERE reservasjons_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $group = $stmt->fetch()['gruppe_kode'];
        } else
            return false;

        return $this->leaderOk($group, $user);
    }

    public function deleteReservation($id)
    {
        $stmt = $this->db->prepare("DELETE FROM reservasjoner WHERE reservasjons_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteRoom($id)
    {
        $stmt = $this->db->prepare("DELETE FROM reservasjoner WHERE rom_kode = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM rom_utstyr WHERE rom_kode = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM sist_reserverte WHERE rom_kode = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM rom WHERE rom_kode = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function editRoom($id, $room_name, $room_type, $cap_ex, $cap_lect, $campus_id, $active)
    {
        $stmt = $this->db->prepare("UPDATE rom SET rom_navn=:name,rom_type_kode=:type,kapasitet_eks=:ex,kapasitet_und=:lect,campus_id=:campus,er_aktiv=:active WHERE rom_kode=:id");

        $stmt->bindParam(':id', $id, PDO::PARAM_STR, 25);
        $stmt->bindParam(':name', $room_name, PDO::PARAM_STR, 25);
        $stmt->bindParam(':type', $room_type, PDO::PARAM_STR, 25);
        $stmt->bindParam(':ex', $cap_ex, PDO::PARAM_STR, 25);
        $stmt->bindParam(':lect', $cap_lect, PDO::PARAM_STR, 25);
        $stmt->bindParam(':campus', $campus_id, PDO::PARAM_STR, 25);
        $stmt->bindParam(':active', $active, PDO::PARAM_STR, 25);

        return $stmt->execute();
    }

    public function createRoom($id, $room_name, $room_type, $cap_ex, $cap_lect, $campus_id, $active)
    {
        $stmt = $this->db->prepare("INSERT INTO rom (rom_kode, rom_navn, rom_type_kode, kapasitet_eks, kapasitet_und, campus_id, er_aktiv) VALUES (:id, :name, :type, :ex, :lect, :campus, :active)");

        $stmt->bindParam(':id', $id, PDO::PARAM_STR, 25);
        $stmt->bindParam(':name', $room_name, PDO::PARAM_STR, 25);
        $stmt->bindParam(':type', $room_type, PDO::PARAM_STR, 25);
        $stmt->bindParam(':ex', $cap_ex, PDO::PARAM_STR, 25);
        $stmt->bindParam(':lect', $cap_lect, PDO::PARAM_STR, 25);
        $stmt->bindParam(':campus', $campus_id, PDO::PARAM_STR, 25);
        $stmt->bindParam(':active', $active, PDO::PARAM_STR, 25);

        return $stmt->execute();
    }

    /**
     * Takes provided reservation object and puts it in database, CHECK FIRST IF OK, NO CHECK HERE!!!.
     *
     * Takes provided reservation object and puts it in database. Be sure to check first if it's correct as there are
     * no safety checks here! True if successful.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param Reservation $reservation Reservation object containing all necessary data.
     * @return boolean TRUE if successful, FALSE if there was error.
     */
    public function makeReservation($reservation)
    {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $stmt = $this->db->prepare("INSERT INTO reservasjoner (fra, til, rom_kode, gruppe_kode, gruppe_leder,
                                                formal) VALUES (:from, :to, :room, :group, :leader, :purpose)");

        $stmt->bindParam(':from', $reservation->getFrom(), PDO::PARAM_STR, 20);
        $stmt->bindParam(':to', $reservation->getTo(), PDO::PARAM_STR, 20);
        $stmt->bindParam(':room', $reservation->getRoom(), PDO::PARAM_INT, 10);
        $stmt->bindParam(':group', $reservation->getGroup(), PDO::PARAM_INT, 11);
        $stmt->bindParam(':leader', $reservation->getLeader(), PDO::PARAM_STR, 10);
        $stmt->bindParam(':purpose', $reservation->getPurpose(), PDO::PARAM_STR, 255);

        return $stmt->execute();
    }

    public function editReservation($id, $from, $to, $room)
    {
        $stmt = $this->db->prepare("UPDATE reservasjoner SET fra=:from,til=:to,rom_kode=:room WHERE reservasjons_id=:id");
        $stmt->bindParam(':from', $from, PDO::PARAM_STR, 25);
        $stmt->bindParam(':to', $to, PDO::PARAM_STR, 25);
        $stmt->bindParam(':room', $room, PDO::PARAM_STR, 25);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR, 25);

        return $stmt->execute();
    }

    public function datetimeToSeperateStrings($date)
    {
        $return = array();
        $temp1 = preg_split("/ |T/", $date);
        $return[0] = $temp1[0];
        $temp2 = explode(':', $temp1[1]);
        $return[1] = $temp2[0];
        $return[2] = $temp2[1];

        return $return;
    }

    /**
     * Fetches the groups the specified user is a member of, and returns them in an array.
     *
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::Prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $user_code The user code of the user
     * @return array consisting of the users groups
     */
    public function getGroups($user_code)
    {
        $stmt = $this->db->prepare("SELECT * FROM grupper WHERE bruker_kode = :bruker_kode");
        $stmt->bindParam(':bruker_kode', $user_code, PDO::PARAM_STR);
        $stmt->execute();
        $groups = array();
        try {
            while ($group = $stmt->fetchObject('Gruppe')) {
                $groups[$group->getGroupCode()] = $group;
            }
            return $groups;
        } catch (Exception $e) {
        }
    }

    /**
     * Fetches all groups from the database
     *
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::Prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @return array consisting of all groups
     */
    public function getAllGroups()
    {
        $stmt = $this->db->prepare("SELECT DISTINCT gruppe_kode FROM grupper");
        $stmt->execute();
        $groups = array();

        while ($groupId = $stmt->fetch(PDO::FETCH_ASSOC)['gruppe_kode']) {
            $groups[$groupId] = new Group($groupId);
        }

        $stmt = $this->db->prepare("SELECT * FROM grupper");
        $stmt->execute();

        while ($group = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groups[$group['gruppe_kode']]->addUser($group['bruker_kode']);
        }

        return $groups;
    }

    /**
     * Fetches all users from the database
     *
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::Prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @return array consisting of all users
     */
    public function getAllUsers()
    {
        $users = array();

        $stmt = $this->db->prepare("SELECT * FROM brukere");
        $stmt->execute();
        while ($user = $stmt->fetchObject('User')) {
            $users[$user->getUserCode()] = $user;
        }

        return $users;
    }

    /**
     * Fetches a single user from the database
     *
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::Prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @return User representing the user
     */
    public function getUser($user_code)
    {
        $stmt = $this->db->prepare("SELECT * FROM brukere where bruker_kode = :user_code");
        $stmt->bindParam(':user_code', $user_code, PDO::PARAM_STR, 25);
        $stmt->execute();

        return $stmt->fetchObject('User');
    }

    /**
     * Fetches all reservations from the database
     *
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::Prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @return array consisting of all reservations
     */
    public function getAllReservations()
    {
        $rooms = array();

        $stmt = $this->db->prepare("SELECT * FROM reservasjoner");
        $stmt->execute();
        while ($reservation = $stmt->fetchObject('Reservation')) {
            $reservations[$reservation->getId()] = $reservation;
        }

        return $reservations;
    }

    /**
     * Fetches all reservations from the database
     *
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::Prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @return array consisting of all reservations
     */
    public function getReservation($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM reservasjoner WHERE reservasjons_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchObject('Reservation');
    }

    public function partOfGroup($groupId, $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM grupper WHERE gruppe_kode = :id");
        $stmt->bindParam(':id', $groupId);
        $stmt->execute();

        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)['bruker_kode']) {
            if ($user == $userId) {
                return true;
            }
        }

        return false;
    }


    public function setLastReserved($usercode, $room)
    {
        $stmt = $this->db->prepare("SELECT * FROM sist_reserverte WHERE rom_kode = :room AND bruker_kode = :user");
        $stmt->bindParam(':user', $usercode, PDO::PARAM_STR, 40);
        $stmt->bindParam(':room', $room, PDO::PARAM_STR, 10);
        $stmt->execute();

        if ($stmt->fetch(PDO::FETCH_ASSOC)['rom_kode'] == false) {
            $stmt = $this->db->prepare("INSERT INTO sist_reserverte (rom_kode, bruker_kode) VALUES (:room, :user)");
            $stmt->bindParam(':user', $usercode, PDO::PARAM_STR, 40);
            $stmt->bindParam(':room', $room, PDO::PARAM_STR, 10);
            return $stmt->execute();
        }

        return true;
    }


    public function getLastReserved($usercode)
    {
        $stmt = $this->db->prepare("SELECT rom_kode FROM sist_reserverte WHERE bruker_kode = :user");
        $stmt->bindParam(':user', $usercode, PDO::PARAM_STR, 40);
        $stmt->execute();

        $temp = array();

        while ($room = $stmt->fetch(PDO::FETCH_ASSOC)['rom_kode']) {
            $temp[] = $room;
        }

        $rooms = array();

        foreach ($temp as $room) {
            $stmt = $this->db->prepare("SELECT * FROM rom WHERE rom_kode = :room");
            $stmt->bindParam(':room', $room, PDO::PARAM_STR, 10);
            $stmt->execute();

            $rooms[] = $stmt->fetchObject('Room');
        }

        return $rooms;
    }

    public function getUserReservations($groups)
    {
        $reservations = array();

        foreach ($groups as $group) {
            $groupId = $group->getGroupCode();
            $stmt = $this->db->prepare("SELECT * FROM reservasjoner WHERE gruppe_kode = :group");
            $stmt->bindParam(':group', $groupId, PDO::PARAM_STR, 11);
            $stmt->execute();

            while ($reservation = $stmt->fetchObject('Reservation')) {
                $reservations[] = $reservation;
            }
        }

        return $reservations;
    }

    /**
     * Adds a new group with the user as a member
     *
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::Prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $user_code The user which will be added to the new group
     * @return boolean true if adding the group succeeds, false if it fails
     */
    public function createNewGroup($user_code, $name)
    {
        $stmt = $this->db->prepare("INSERT INTO grupper (bruker_kode)  VALUE (:bruker_kode) ");
        $stmt->bindParam(':bruker_kode', $user_code);
        $stmt->execute();

        $id = $this->db->lastInsertId();

        $stmt = $this->db->prepare("INSERT INTO gruppenavn (gruppe_kode, gruppenavn)  VALUE (:id, :name) ");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Fetches the roominfo of the specified room from the database
     *
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::Prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $room_code The room code of the room
     * @return Room object containing the information for the room
     */
    public function getRoomInfo($room_code)
    {
        require_once "Room.class.php";
        $stmt = $this->db->prepare("SELECT * FROM rom WHERE rom_kode = :rom_kode");
        $stmt->bindParam(':rom_kode', $room_code, PDO::PARAM_STR);
        $stmt->execute();
        try {
            return $stmt->fetchObject('Room');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Fetches the types rooms available from the database
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::Prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @return array of Strings containing the types of rooms
     */
    public function getRoomTypes()
    {
        $stmt = $this->db->prepare("SELECT * FROM rom_typer");
        $stmt->execute();

        $types = array();

        while ($type = $stmt->fetch(PDO::FETCH_ASSOC)['rom_type_kode']) {
            $types[] = $type;
        }

        return $types;
    }

    public function getCampuses()
    {
        $stmt = $this->db->prepare("SELECT * FROM campus");
        $stmt->execute();

        $types = array();

        while ($type = $stmt->fetch(PDO::FETCH_ASSOC)['campus_id']) {
            $types[] = $type;
        }

        return $types;
    }

    public function getEquipmentTypes()
    {
        $equipments = array();

        $stmt = $this->db->prepare("SELECT * FROM utstyr");
        $stmt->execute();
        while ($equipment = $stmt->fetchObject('Equipment'))
            $equipments[$equipment->getEquipmentCode()] = $equipment;

        return $equipments;
    }


    public function getGroup($group_code)
    {
        $stmt = $this->db->prepare("SELECT * FROM grupper WHERE gruppe_kode = :gruppe_kode");
        $stmt->bindParam(':gruppe_kode', $group_code, PDO::PARAM_INT);
        $stmt->execute();
        $groupArray = array();
        $i = 0;
        while ($group = $stmt->fetchObject('Gruppe')) {
            $groupArray[$i++] = $group;
        }
        return $groupArray;
    }

    public function getFutureReservations($group_code)
    {
        $stmt = $this->db->prepare('SELECT * FROM reservasjoner where gruppe_kode = :gruppe_kode');
        $stmt->bindParam(':gruppe_kode', $group_code, PDO::PARAM_INT);
        $stmt->execute();

        $reservations = array();
        while ($reservation = $stmt->fetchObject('Reservation')) {
            $timeAndDate = explode(' ', $reservation->getFrom());
            if ($timeAndDate[0] >= date('Y-m-d')) {
                $reservations[$reservation->getId()] = $reservation;
            }
        }
        return $reservations;
    }

    public function getFutureRoomReservations($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM reservasjoner where rom_kode = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $reservations = array();
        while ($reservation = $stmt->fetchObject('Reservation')) {
            $timeAndDate = explode(' ', $reservation->getFrom());
            if ($timeAndDate[0] >= date('Y-m-d')) {
                $reservations[$reservation->getId()] = $reservation;
            }
        }
        return $reservations;
    }

    /**
     * Checks if username is taken.
     *
     * Checks if username is taken by fetching user from database with same username,
     * if it fails, then username is not taken and it will return false. Whereas if
     * it succeeds it will return true, indicating taken username.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $username User's name.
     * @return boolean, true if username taken, false otherwise.
     */
    public function usernameTaken($username)
    {
        $posts = array();

        $stmt = $this->db->prepare("SELECT * FROM brukere WHERE LOWER(bruker_kode) = LOWER(:username)");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR, 25);
        $stmt->execute();

        return $stmt->fetchObject('User');
    }

    /**
     * Checks if email is taken.
     *
     * Checks if email is taken by fetching user from database with same email,
     * if it fails, then email is not taken and it will return false. Whereas if
     * it succeeds it will return true, indicating taken email.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $email User's email address.
     * @return bool , true if email taken, false otherwise.
     */
    public function emailTaken($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM brukere WHERE LOWER(epost) = LOWER(:email)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR, 50);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Takes provided user object and puts it in database, CHECK FIRST IF OK, NO CHECK HERE!!!.
     *
     * Takes provided user object and puts it in database. Be sure to check first if it's correct as there are
     * no safety checks here! True if successful.
     *
     * @since 1.0.0
     * @access public
     *
     * @see PDO::prepare
     * @link http://php.net/manual/en/pdo.prepare.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param User $user User object containing name, email, password and rand value.
     * @return boolean TRUE if successful, FALSE if there was error.
     */
    public function addUser($user)
    {
        $stmt = $this->db->prepare("INSERT INTO brukere (bruker_kode, fornavn, etternavn, epost, bruker_type_id, passord, random) VALUES (:usercode, :firstname, :lastname, :email, :usertype, :password, :rand)");

        $stmt->bindParam(':usercode', strtolower($user->getUserCode()), PDO::PARAM_STR, 40);
        $stmt->bindParam(':firstname', ucfirst($user->getFirstName()), PDO::PARAM_STR, 50);
        $stmt->bindParam(':lastname', ucfirst($user->getLastName()), PDO::PARAM_STR, 50);
        $stmt->bindParam(':email', strtolower($user->getEmail()), PDO::PARAM_STR, 50);
        $stmt->bindParam(':usertype', $user->getUserType(), PDO::PARAM_STR, 4);
        $stmt->bindParam(':password', $user->getPassword(), PDO::PARAM_INT, 255);
        $stmt->bindParam(':rand', $user->getRandom(), PDO::PARAM_INT, 40);

        return $stmt->execute();
    }

    /**
     * Takes provided user object and sends confirmations email to user.
     *
     * Takes provided user object and fetche suser email from user object and
     * sends confirmation to this address with random value and name as a way
     * of confirming users email validity.
     *
     * @since 1.0.0
     * @access public
     *
     * @see mail
     * @link http://php.net/manual/en/function.mail.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param User $user User object containing all necessary data.
     * @return boolean TRUE if successful, FALSE if there was error.
     */
    public function sendEmailConfirmation($user)
    {
        $mail = new PHPMailer;

        $mail->isSMTP();                                                // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com;smtp-relay.gmail.com';            // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                         // Enable SMTP authentication
        $mail->Username = 'sysut2016gruppe1@gmail.com';                 // SMTP username
        $mail->Password = 'Gruppe2SuggerBaller';                        // SMTP password
        $mail->SMTPSecure = 'tls';                                      // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                              // TCP port to connect to
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('sysut2016gruppe1@gmail.com', 'RoomBooking');    // Add a sender
        $mail->addAddress($user->getEmail());                           // Add a recipient

        $mail->isHTML(true);                                            // Set email format to HTML

        $mail->Subject = 'Rom reservasjoner bekreftelse epost';
        $mail->Body = $this->makeConfirmMessage($user);

        if (!$mail->send()) {
            $this->deleteUser($user);
            return false;
        } else {
            return true;
        }
    }

    private function makeConfirmMessage($user)
    {
        $username = $user->getUsercode();
        $userRandValue = $user->getRandom();
        $address = $this->site . "confirmemail.php?rand=" . $userRandValue . "&username=" . $username;

        return "Hei " . $username . "!<br/>" .
        "Takk for at du har opprettet konto på systemet vårt, før du kan bruke den må du bekrefte epost-adressen din.<br/>" .
        "Du kan gjøre dette ved å klikke på linken nedenfor:<br/>" .
        "____________________________________________________________________<br/><br/>" .
        "Thanks for joining our room booking system. We need you to confirm your email before you can use your account.<br/>" .
        "You can do so by clicking the link provided below:<br/>" .
        "____________________________________________________________________<br/><br/>" .
        "<a href=" . $address . ">" . $address . "</a><br/><br/>" .
        "____________________________________________________________________<br/><br/>" .
        "Ha en fin dag videre.<br/>Have a nice day.";
    }

    /**
     * Takes provided reservation object and sends confirmations to every group member.
     *
     * Takes provided reservation object and fetches every user email from database and sends confirmation to every
     * group member.
     *
     * @since 1.0.0
     * @access public
     *
     * @see mail
     * @link http://php.net/manual/en/function.mail.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param Reservation $reservation Reservation object containing all necessary data.
     * @return boolean TRUE if successful, FALSE if there was error.
     */
    public function sendConfirmation($reservation)
    {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $stmt = $this->db->prepare("SELECT epost FROM brukere WHERE bruker_kode IN (SELECT bruker_kode
                                                  FROM grupper WHERE gruppe_kode = :group);");
        $stmt->bindParam(':group', $reservation->getGroup(), PDO::PARAM_INT, 11);

        $mail = new PHPMailer;

        $mail->isSMTP();                                                // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com;smtp-relay.gmail.com';            // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                         // Enable SMTP authentication
        $mail->Username = 'sysut2016gruppe1@gmail.com';                 // SMTP username
        $mail->Password = 'Gruppe2SuggerBaller';                        // SMTP password
        $mail->SMTPSecure = 'tls';                                      // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                              // TCP port to connect to
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('sysut2016gruppe1@gmail.com', 'RoomBooking');    // Add a sender

        if ($stmt->execute()) {
            while ($email = $stmt->fetch(PDO::FETCH_ASSOC))
                $mail->addAddress($email['epost']);
        }

        $mail->isHTML(true);                                            // Set email format to HTML

        $mail->Subject = 'Reservajonsbekreftelse';
        $mail->Body = $this->makeMessage($reservation);

        return $mail->send();
    }

    private function makeMessage($reservation)
    {
        $room = $reservation->getRoom();
        $from = $reservation->getFrom();
        $end = $reservation->getTo();
        $group = $this->getGroupName($reservation->getGroup());
        $leader = $this->getLeaderName($reservation->getLeader());
        $purpose = $reservation->getPurpose();
        $address = $this->site . "user.php";

        return "Din reservasjon av rom: " . $room . "<br/>" .
        "Fra: " . $from . "<br/>" .
        "Til: " . $end . "<br/>" .
        "Gruppe: " . $group . "<br/>" .
        "Gruppeleder: " . $leader . "<br/>" .
        "Formal: " . $purpose . "<br/>" .
        "Er godkjent, se dine reservasjoner og endre dem her: <a href=" . $address . ">" . $address . "</a><br/><br/>" .
        "Your reservation of room: " . $room . "<br/>" .
        "From: " . $from . "<br/>" .
        "To: " . $end . "<br/>" .
        "Group: " . $group . "<br/>" .
        "Group leader: " . $leader . "<br/>" .
        "Purpose: " . $purpose . "<br/>" .
        "Is accepted, see your reservations and edit them here: <a href=" . $address . ">" . $address . "</a>";
    }

    public function deleteUser($user_code)
    {
        $stmt = $this->db->prepare("DELETE FROM grupper WHERE bruker_kode=:user_code");
        $stmt->bindParam(':user_code', $user_code, PDO::PARAM_STR, 40);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM sist_reserverte WHERE bruker_kode=:user_code");
        $stmt->bindParam(':user_code', $user_code, PDO::PARAM_STR, 40);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM reservasjoner WHERE gruppe_leder=:user_code");
        $stmt->bindParam(':user_code', $user_code, PDO::PARAM_STR, 40);
        $stmt->execute();

        $stmt = $this->db->prepare("DELETE FROM brukere WHERE bruker_kode=:user_code");
        $stmt->bindParam(':user_code', $user_code, PDO::PARAM_STR, 40);

        return $stmt->execute();
    }

    public function editUser($user_code, $first_name, $last_name, $user_type, $verified)
    {
        $stmt = $this->db->prepare("UPDATE brukere SET fornavn=:first_name, etternavn=:last_name, bruker_type_id=:user_type, verifisert=:verified WHERE bruker_kode=:user_code");
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR, 50);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR, 50);
        $stmt->bindParam(':user_type', $user_type, PDO::PARAM_INT, 4);
        $stmt->bindParam(':verified', $verified, PDO::PARAM_STR, 1);
        $stmt->bindParam(':user_code', $user_code, PDO::PARAM_STR, 40);

        return $stmt->execute();
    }

    public function sendPassword($user)
    {
        $stmt = $this->db->prepare("SELECT epost, random FROM brukere WHERE bruker_kode = :user");
        $stmt->bindParam(':user', $user, PDO::PARAM_INT, 40);
        $stmt->execute();
        $temp = $stmt->fetch(PDO::FETCH_ASSOC);

        $mail = new PHPMailer;

        $mail->isSMTP();                                                // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com;smtp-relay.gmail.com';            // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                         // Enable SMTP authentication
        $mail->Username = 'sysut2016gruppe1@gmail.com';                 // SMTP username
        $mail->Password = 'Gruppe2SuggerBaller';                        // SMTP password
        $mail->SMTPSecure = 'tls';                                      // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                              // TCP port to connect to
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('sysut2016gruppe1@gmail.com', 'RoomBooking');    // Add a sender

        $mail->addAddress($temp['epost']);

        $mail->isHTML(true);                                            // Set email format to HTML

        $mail->Subject = 'Glemt passord';
        $mail->Body = $this->makePasswordMessage($user, $temp['random']);

        return $mail->send();
    }

    private function makePasswordMessage($user, $rand)
    {
        $address = $this->site . "forgotPassword.php?rand=" . $rand . "&user=" . $user;

        return "For å nullstille passordet gå her:<br/>" .
        "Du har bedt om å nullstille passordet ditt.<br/>" .
        "Du kan gjøre dette ved å klikke på linken nedenfor:<br/>" .
        "____________________________________________________________________<br/><br/>" .
        "You have asked to reset your password.<br/>" .
        "You can do so by clicking the link provided below:<br/>" .
        "____________________________________________________________________<br/><br/>" .
        "<a href=" . $address . ">" . $address . "</a><br/><br/>" .
        "____________________________________________________________________<br/><br/>" .
        "Hvis det var ikke deg som har bedt om dette kan du ignorere denne eposten. Ha en fin dag videre.<br/>" .
        "If it wasn't you, you can ignore this email. Have a nice day.";
    }

    public function changePassword($user, $password)
    {
        $stmt = $this->db->prepare("UPDATE brukere SET passord = :password WHERE bruker_kode = :user");
        $stmt->bindParam(':password', $password, PDO::PARAM_STR, 255);
        $stmt->bindParam(':user', $user, PDO::PARAM_STR, 40);

        return $stmt->execute();
    }

    /**
     * Takes provided username and users random value and confirms email if all
     * is correct.
     *
     * Takes provided username and users random value and confirms email if all
     * is correct. If email was already confirmed 2 is returned indicating it.
     *
     * @since 1.0.0
     * @access public
     *
     * @see mail
     * @link http://php.net/manual/en/function.mail.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $usercode Username provided at signup.
     * @param string $rand Users unique random value assigned at signup.
     * @return int 1 if confirmed, 2 if already confirmed, 0 if fail.
     */
    public function confirmEmail($usercode, $rand)
    {
        $stmt = $this->db->prepare("SELECT * FROM brukere WHERE bruker_kode = :usercode AND random = :rand");
        $stmt->bindParam(':usercode', $usercode, PDO::PARAM_STR, 40);
        $stmt->bindParam(':rand', $rand, PDO::PARAM_STR, 40);
        $stmt->execute();

        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($result['verifisert']) {
                return 2;
            } else {
                $stmt = $this->db->prepare("UPDATE brukere SET verifisert = 1 WHERE bruker_kode = :usercode");
                $stmt->bindParam(':usercode', $usercode, PDO::PARAM_STR, 40);
                if ($stmt->execute()) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    /**
     * Takes provided username and password, and checks if credentials are correct.
     *
     * Takes provided username and password and checks if they are correct as well
     * as if user has verified his email.
     *
     * @since 1.0.0
     * @access public
     *
     * @see mail
     * @link http://php.net/manual/en/function.mail.php
     * @global PDO $db Represents a connection between PHP and a database server.
     *
     * @param string $username Username provided at signup.
     * @param string $password Users password.
     * @return true if correct, false otherwise
     */
    public function validateCredentials($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM brukere WHERE bruker_kode = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR, 25);
        $stmt->execute();

        if ($user = $stmt->fetchObject('User')) {
            if ($user->isVerified() == false) {
                return -1;
            }

            if ($password == null) {
                return null;
            }

            $hash = $user->getPassword();
            if (password_verify($password, $hash) == true) {
                return $user;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getRoomEquipment($room_code)
    {
        $stmt = $this->db->prepare("SELECT utstyr_navn
        FROM utstyr WHERE utstyr_kode
        IN (SELECT utstyr_kode FROM rom_utstyr WHERE rom_kode = :room_code)");
        $stmt->bindParam(':room_code', $room_code, PDO::PARAM_STR, 25);
        $stmt->execute();
        $equipment = array();
        while ($string = $stmt->fetch(PDO::FETCH_ASSOC)['utstyr_navn']) {
            $equipment[] = $string;
        }
        return $equipment;
    }

    public function getUserTypes()
    {
        $stmt = $this->db->prepare("SELECT * FROM bruker_typer");
        $stmt->execute();
        $typer = array();

        while ($type = $stmt->fetch(PDO::FETCH_ASSOC)['bruker_type_navn']) {
            $typer[] = $type;
        }

        return $typer;
    }

    public function removeEquipment($id)
    {
        $stmt = $this->db->prepare("DELETE FROM rom_utstyr WHERE rom_kode = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR, 25);
        return $stmt->execute();
    }

    public function addEquipment($id, $equipment)
    {
        $stmt = $this->db->prepare("INSERT INTO rom_utstyr (utstyr_kode, rom_kode) VALUES (:equipment, :id)");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR, 25);
        $stmt->bindParam(':equipment', $equipment, PDO::PARAM_STR, 25);
        return $stmt->execute();
    }
}