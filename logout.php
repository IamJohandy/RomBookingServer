<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 08.05.2016
 * Time: 15.11
 */

require_once 'base.php';

$result = array();

if($session->get('loggedIn') == false)
{
    $result['success'] = 0;
    $result['message'] = "Already logged out";
    print(json_encode($result, JSON_PRETTY_PRINT));
    exit;
}
else
{
    //$session->set('loggedIn', false);
    $session->clear();
    $session->invalidate();

    $result['success'] = 1;
    $result['message'] = "Logged out";
    print(json_encode($result, JSON_PRETTY_PRINT));
    exit;
}
