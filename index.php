<?php
# This function reads your DATABASE_URL configuration automatically set by Heroku
# the return value is a string that will work with pg_connect
if (isset($_SERVER["CLEARDB_DATABASE_URL"])) {
  $db = parse_url($_SERVER["CLEARDB_DATABASE_URL"]);
  define("DB_NAME", trim($db["path"],"/"));
  define("DB_USER", $db["user"]);
  define("DB_PASSWORD", $db["pass"]);
  define("DB_HOST", $db["host"]);
}
else {
  die("Your heroku CLEARDB_DATABASE_URL does not appear to be correctly specified.");
}


function adminer_object() {
  class AdminerSoftware extends Adminer {
    function name() {
      // custom name in title and heading
      return 'Strepiny';
    }
    function credentials() {
      // server, username and password for connecting to database
      return array(DB_HOST, DB_USER, DB_PASSWORD);
    }
    function login($login, $password) {
      // validate user submitted credentials
      return ($login == 'root' && $password == '');
    }
  }
  return new AdminerSoftware;
}

include "./adminer.php";