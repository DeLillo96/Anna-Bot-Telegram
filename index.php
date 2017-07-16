<?php

require_once __DIR__ . '/Anna/Utility/PostgreSQLConnector.php';

$db = new \Anna\Utility\PostgreSQLConnector();
var_dump($db->read());