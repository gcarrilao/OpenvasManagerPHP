<?php

require_once __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer autoload

use Openvas\OpenvasManager;

$ov = new OpenvasManager("localhost","9390","admin","admin");

//print_r($ov->get_version());

$options= array(
	"complexity" => false,
	"host" => "10.3.8.199",
);

//print_r($ov->get_targets($options));


$options= array(
	"complexity" => False,
	"report_id" => "ae2a11cd-495d-419e-94e2-2158197ec97f",
	 "format_id" => "c1645568-627a-11e3-a660-406186ea4fc5"
);

print_r($ov->get_reports($options));
