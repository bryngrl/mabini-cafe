<?php
require("../vendor/autoload.php");

$openapi = \OpenApi\Generator::scan([__DIR__ . "/../Controllers"]);

header('Content-Type: application/json');
echo $openapi->toJSON();
