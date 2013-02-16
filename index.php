<?php
# This function reads your DATABASE_URL configuration automatically set by Heroku
# the return value is a string that will work with pg_connect
function pg_connection_string() {
  // we will fill this out next
}
# Establish db connection
$db = mysql_connect(mysql_connection_string());
if (!$db) {
   echo "Database connection error."
   exit;
}
$result = mysql_query($db, "SELECT statement goes here");


