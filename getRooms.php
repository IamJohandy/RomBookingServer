<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 09.05.2016
 * Time: 19:11
 */

require_once 'base.php';

if($session->get('loggedIn') == false)
{
    $result['success'] = 0;
    $result['message'] = "Not logged in";
    print(json_encode($result, JSON_PRETTY_PRINT));
    exit;
}

$rooms = $helper->getAllRooms($session->get('user'));
$result = array();
$result['success'] = 1;
$result['rooms'] = array();

foreach($rooms as $room)
{
    array_push($result['rooms'], $room->toArray());
}

print(json_encode($result, JSON_PRETTY_PRINT));