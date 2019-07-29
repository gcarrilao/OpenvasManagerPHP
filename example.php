<?php 

require_once __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer autoload

use Openvas\OpenvasManager;

$ov = new OpenvasManager("localhost","9390","admin","admin");

print_r($ov->get_version());
