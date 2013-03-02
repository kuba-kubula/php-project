<!DOCTYPE html>
<?php
require_once("_settings/settings.php");
include("svg/hexagon.php");

?>
<html>
  <head>
    <meta charset="UTF-8">
<?php echo ($_GET["robot"]=='true')?'<meta http-equiv="refresh" content="'.$robotTime.'">':''; ?>
    <title>Moloch - Probe Situation</title>
    <link rel="stylesheet" type="text/css" href="strepiny.css">

    <script type="text/javascript" src="glitch/javascript/glitch/html2canvas.js"></script>
    <script type="text/javascript" src="glitch/javascript/glitch/glitch-lib.js"></script>
    <script type="text/javascript" src="glitch/javascript/glitch/glitch-execute.js"></script>

   </head>


   <body bgcolor="#0b0d0d" style="border: 0; margin: 0; padding: 0; overflow:hidden; font-family: Omikron, omikron-webfont, sans-serif;"><div id="projection">
     <svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="768" width="1024" style="border:none; margin:0; padding:0;">
     <g transform="translate (512, 384)">

<?php
include("svg/moloch.php");

$sondymis = array();
$sondyret = array();
$sondyall = $db->fetchAssoc('SELECT * FROM strepiny_sondy');

foreach ($sondyall as $sonda) {
  if ($sonda['stav'] == 'MIS') {
    $sondymis[] = $sonda;
  }
  elseif ($sonda['stav'] == 'RTN') {
    $sondyret[] = $sonda;
  }
}

foreach($sondymis as $sondamis) {
	echo '<line x1="'.uhel2x(0).'" y1="'.uhel2y(0).'" x2="'.uhel2x(sector2uhel($sondamis['cil'])).'" y2="'.uhel2y(sector2uhel($sondamis['cil'])).'" style="stroke:rgb(009,241,189);stroke-width:5" stroke-opacity="0.5"/>';
	smallhexagon('#00ff00',$sondamis['id'],
		uhel2x(sector2uhel($sondamis['cil']))*$sondamis['progres']/100,
		((uhel2y(sector2uhel($sondamis['cil']))-uhel2y(0))*$sondamis['progres']/100)+uhel2y(0)
	);

}

foreach($sondyret as $sondaret) {
	echo '<line x1="'.uhel2x(0).'" y1="'.uhel2y(0).'" x2="'.uhel2x(sector2uhel($sondaret['cil'])).'" y2="'.uhel2y(sector2uhel($sondaret['cil'])).'" style="stroke:rgb(009,241,189);stroke-width:5" stroke-opacity="0.5"/>';
	smallhexagon('#00aeff',$sondaret['id'],
		uhel2x(sector2uhel($sondaret['cil']))*$sondaret['progres']/100,
		((uhel2y(sector2uhel($sondaret['cil']))-uhel2y(0))*$sondaret['progres']/100)+uhel2y(0)
	);

}

?>
</g>


<?php

foreach($sondyall as $sonda) {

	echo '<g transform="translate ('.$sonda['display_x'].','.$sonda['display_y'].')">';
	$label='<text x="-14" y="-6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 35px; fill:black">'.'0'.$sonda['id'].'</text><text x="-19" y="12" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 20px; fill:black">probe</text>';
  largehexagon('#09f1bb',$label,0,-57);

  if ($sonda['slot1'] == 'NIL') {
	  $image = '';
  } else {
	  $image='<image x="-31" y="-35" width="62" height="70" xlink:href="img/'.$sonda['slot1'].'.png" />';
  }
  largehexagon('#09f1bb',$image,-33,0);
  if ($sonda['slot2'] == 'NIL') {
	  $image = '';
  }
  else {
	  $image='<image x="-31" y="-35" width="62" height="70" xlink:href="img/'.$sonda['slot2'].'.png" />';
  }
  largehexagon('#09f1bb',$image,33,0);

  switch ($sonda['stav']) {
    case 'HOM':
      $color="#ff7800";
      $label='<text x="-29" y="0" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; fill:black">standby</text>';
      break;
    case 'MIS':
      $color="#00ff00";
      $label='<text x="-29" y="0" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; fill:black">mission</text>';
      break;
    case 'RTN':
      $color="#00aeff";
      $label='<text x="-29" y="-6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; fill:black">return</text>';
      $label.='<text x="-29" y="6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 17px; fill:black">to base</text>';
      break;
    case 'ARM':
      $color="#ff0000";
      $label='<text x="-6" y="-6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; fill:black">re</text>';
      $label.='<text x="-29" y="6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 17px; fill:black">arming</text>';
      break;
    case 'NIL':
      $color="#707070";
      $label='<text x="-15" y="-6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; fill:black">not</text>';
      $label.='<text x="-18" y="6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 17px; fill:black">avail</text>';
      break;
  }

  largehexagon($color,$label,0,57);
  echo '</g>';
  smallhexagon($color, $sonda['id'], 512 - 16 + (($sonda['id'] - 3) * 34), 142);
}

?>
    </svg>
   </div></body>
</head>
</html>
