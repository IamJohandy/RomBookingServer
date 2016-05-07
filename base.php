<?php

spl_autoload_register( function ($class_name) {
require_once 'classes/' .$class_name . '.class.php';
});

require_once 'vendor/autoload.php';
require_once 'auth.php';

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

$helper = new Helper($db);

$session = new Session();
$session->start();

?>