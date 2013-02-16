<?php
# This function reads your CLEARDB_DATABASE_URL configuration automatically set by Heroku
if (isset($_SERVER["CLEARDB_DATABASE_URL"])) {
  $db = parse_url($_SERVER["CLEARDB_DATABASE_URL"]);
  define("CLEAR_DB_NAME", trim($db["path"],"/"));
  define("CLEAR_DB_USER", $db["user"]);
  define("CLEAR_DB_PASSWORD", $db["pass"]);
  define("CLEAR_DB_HOST", $db["host"]);
}
else {
  die("Your heroku CLEARDB_DATABASE_URL does not appear to be correctly specified.");
}


function adminer_object() {
  class AdminerSoftware extends Adminer {
    function credentials() {
      // server, username and password for connecting to database
      return array(CLEAR_DB_HOST, CLEAR_DB_USER, CLEAR_DB_PASSWORD);
    }
    function database() {
      return CLEAR_DB_NAME;
    }
    function login($login, $password) {
      // validate user submitted credentials
      return ($login == $_SERVER['LOGIN_USER'] || $login == $_SERVER['LOGIN_USER2']);
    }
  }
  return new AdminerSoftware;
}

include "./adminer.php";