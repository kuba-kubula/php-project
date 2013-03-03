<?php
require_once("_settings/settings.php");
include("svg/hexagon.php");

$nestabilita = $db->fetch1Assoc('SELECT * FROM '.$db->prefix.'strepiny_nestabilita WHERE id="1";');

?><!doctype html>
<html>
<head>
  <meta charset="UTF-8" />
<?php echo (isset($_GET["robot"]) && $_GET['robot'] == 'true')?'<meta http-equiv="refresh" content="'.$robotTime.'">':''; ?>
  <link rel="stylesheet" type="text/css" href="strepiny.css">
  <title>Nestabilita jadra</title>
</head>

<body bgcolor="#0b0d0d" style="border: 0; margin: 0; padding: 0; overflow:hidden; font-family:Omikron, omikron-webfont; font-size:100px; color:white;">
<center>
<br />celkova nestabilita<br />
<?php echo $nestabilita['nc']; ?><br/>
odber energie<br />
<?php echo 100+$nestabilita['dnc']; ?>%
</center>

</body>
</head>
</html>
