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

    private $site = "https://roombooking-mormonjesus.c9users.io/";
    //private $site = "http://kark.hin.no/~530203/RoomBooking/";

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

    public function validateCredentials($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM brukere WHERE bruker_kode = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR, 25);
        $stmt->execute();

        if ( $user = $stmt->fetchObject('User')){
            if( $user->isVerified() == false) {
                return -1;
            }

            if($password == null){
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
}
