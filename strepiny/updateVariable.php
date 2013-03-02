<?php
require_once("_settings/settings.php");

$nestabilita = $db->fetch1Assoc('SELECT * FROM '.$db->prefix.'strepiny_nestabilita WHERE id="1";');

header('Content-type: text/plain');

if (isset($_POST['varname']) && isset($_POST['varvalue'])) {
  if (isset($nestabilita[$_POST['varname']])) {
    $varname = $_POST['varname'];
    $ar = array();
    $ar[$varname] = (int)$_POST['varvalue'];
    $db->update('strepiny_nestabilita', $ar, "WHERE id='1'");
    die($ar[$varname]);
  }
}
die('Fail');
