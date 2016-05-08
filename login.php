<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 04.05.2016
 * Time: 13.47
 */
error_reporting(E_ALL & ~E_NOTICE);

require_once 'base.php';

$username = $request->request->get('username', 'jdy003');
$password = $request->request->get('password', 'Benis123');

$token = $request->request->get('token', false);

$incorrect = false;
$loggedIn = false;

$result = array();

if($username != false && $password != false)
{
    $user = $helper->validateCredentials($username, $password);
    if( $user != null  && $user != -1)
    {
        $session->migrate(true,0);
        $session->set('loggedIn', true);
        $session->set('user', $user);

        //$session->set('token', $token);

        /*if( $session->get('lastLocation') != false)
        {
            header('Location: ' . $session->get('lastLocation'));
        }
        else
        {
            header('Location: getUser.php');
        }*/

        //header('Location: getUser.php');
        //exit;
        $result['success'] = 1;
        $result['message'] = "Logged in";
    }
    else if( $user == -1 )
    {
        //user is not validated
        $notValidated = true;
        $result['success'] = 0;
        $result['message'] = "Not validated";
    }
    else
    {
        $incorrect = true;
        $result['success'] = 0;
        $result['message'] = "Incorrect credentials";
    }
}
else
{
    $result['success'] = 0;
    $result['message'] = "Missing fields";
}

print(json_encode($result, JSON_PRETTY_PRINT));