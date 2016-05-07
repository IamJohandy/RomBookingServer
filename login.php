<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 04.05.2016
 * Time: 13.47
 */

require_once 'base.php';

$username = $request->request->get('username', false);
$password = $request->request->get('password', false);

$token = $request->request->get('token', false);

$incorrect = false;
$loggedIn = false;

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
            header('Location: index.php');
        }*/
        header('Location: index.php');
        exit;
    }
    else if( $user == -1 )
    {
        $notValidated = true;
    }
    else
    {
        $incorrect = true;
    }
}