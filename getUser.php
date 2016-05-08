<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 03.05.2016
 * Time: 10.59
 */

require_once 'base.php';

$result = array();

if($session->get('loggedIn') == false) {
    //$session->set('lastLocation', 'getUser.php');
    //header("Location: login.php");
    //exit;
    $result['success'] = 0;
    $result['message'] = "Not logged in";
    print(json_encode($result, JSON_PRETTY_PRINT));
    exit;
}

//$session->set('loggedIn', false);

$user = array();
$user['success'] = 1; $user['user'] = $session->get('user')->toArray();

print(json_encode($user, JSON_PRETTY_PRINT));


