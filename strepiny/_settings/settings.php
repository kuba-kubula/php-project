<?php

require( "phpClasses/classDbAdapterMySQL.php" );

$robotTime = 10;
$robotMultiplier = 6;

$sondacfg = array(
	'armspeed'    => 17,
	'armno'       => 50,
  'misspeedmin' => 3,
  'misspeedmax' => 6,
	'misno'       => 10
);

$dbServerHost = "127.0.0.1";
$dbUser = "root";
$dbPass = "";
$dbSchema = "strepiny";
$dbPrefix = '';

if (isset($_SERVER["CLEARDB_DATABASE_URL"])) {
  $db = parse_url($_SERVER["CLEARDB_DATABASE_URL"]);
  $dbSchema = trim($db["path"],"/");
  $dbUser = $db["user"];
  $dbPass = $db["pass"];
  $dbServerHost = $db["host"];
  $dbPrefix = 'new_';
}

$db = new DbAdapterMySQL ($dbServerHost, $dbUser, $dbPass, $dbSchema, $dbPrefix);
