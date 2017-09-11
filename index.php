<?php
/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 20.12.2016
 * Time: 17:28
 */
define("DOC_PATH", substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])) . '/');
require_once('classes/Router.php');
require_once('classes/Controller.php');
session_start();
try
{
    $controller = new Controller();
    $controller->controlURL();
    $router = Router::getRouter();
    $router->checkRout();
}
catch (Exception $e)
{
    echo $e->getMessage();
    die;
}
