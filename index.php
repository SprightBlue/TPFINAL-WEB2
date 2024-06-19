<?php

    include_once("Configuration.php");

    session_start();

$controller = isset($_GET["controller"]) ? $_GET["controller"] : "";
$action = isset($_GET["action"]) ? $_GET["action"] : "";
$idQuestion = isset($_GET["idQuestion"]) ? $_GET["idQuestion"] : null;

$router = Configuration::getRouter();
$router->route($controller, $action, $idQuestion);

?>