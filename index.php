<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'autoload.php';

use core\rest\RestController;

header('Content-type:application/json');

$restController = new RestController();
$restController->handleRequest()->send();

?>