<?php
require 'vendor/autoload.php';
require 'helpers/Constants.php';
require 'helpers/xml2json.php';
require 'classes/BikeStation.class.php';
require 'controllers/BikeController.php';

header('Access-Control-Allow-Origin : *');

$server = new \Jacwright\RestServer\RestServer('debug');
$server->addClass('BikeController');
$server->handle();