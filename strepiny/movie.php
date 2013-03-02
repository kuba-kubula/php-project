<?php
require_once("_settings/settings.php");
include("svg/hexagon.php");
include("svg/moloch.php");

$sondymis = array();
$sondyret = array();
$sondyall = $db->fetchAssoc('SELECT * FROM strepiny_sondy');

$subscribe = md5(serialize($sondyall));

foreach ($sondyall as $sonda) {
  if ($sonda['stav'] == 'MIS') {
    $sondymis[] = $sonda;
  }
  elseif ($sonda['stav'] == 'RTN') {
    $sondyret[] = $sonda;
  }
}

if (isset($_GET['xhr'])) {
  define("XHR", true);
  header("Content-type:text/plain");
}
else {
  define("XHR", false);
}

if (!XHR) { ?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<?php echo ($_GET["robot"]=='true')?'<meta http-equiv="refresh" content="600">':''; ?>
<title>Moloch - Probe Situation</title>
<link rel="stylesheet" type="text/css" href="strepiny.css" />

<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript" src="glitch/javascript/glitch/html2canvas.js"></script>
<script type="text/javascript" src="glitch/javascript/glitch/glitch-lib.js"></script>
<script type="text/javascript" src="glitch/javascript/glitch/glitch-execute.js"></script>
<style type="text/css">
  html, body { font-family: Omikron, omikron-webfont, sans-serif; }
</style>
</head>


<body bgcolor="#0b0d0d" style="border: 0; margin: 0; padding: 0; overflow:hidden; font-family: Omikron, omikron-webfont, sans-serif;">
<div class="projectionSvg">
<div id="projection">
  <canvas id="canvasFin"></canvas>
</div><div>
<?php
}

if (XHR && isset($_GET['lastId'])) {
  if ($_GET['lastId'] == "id".$subscribe) {
    die("0");
  }
}

echo '<svg id="id' . $subscribe . '" xmlns="http://www.w3.org/2000/svg" version="1.1" height="768" width="1024" style="border:none; margin:0; padding:0;">';

$renderingLnes = (count($sondymis) > 0 || count($sondyret) > 0);

// planetarni trasy sond

if ($renderingLnes) {
  echo '<g transform="translate(512, 384)">';
}

foreach($sondymis as $sondamis) {
  echo '<line x1="'.uhel2x(0).'" y1="'.uhel2y(0).'" x2="'.uhel2x(sector2uhel($sondamis['cil'])).'" y2="'.uhel2y(sector2uhel($sondamis['cil'])).'" style="stroke:rgb(009,241,189);stroke-width:5" stroke-opacity="0.5" />';
  smallhexagon('#00ff00',$sondamis['id'],
    uhel2x(sector2uhel($sondamis['cil']))*$sondamis['progres']/100,
    ((uhel2y(sector2uhel($sondamis['cil']))-uhel2y(0))*$sondamis['progres']/100)+uhel2y(0)
  );

}

foreach($sondyret as $sondaret) {
  echo '<line x1="'.uhel2x(0).'" y1="'.uhel2y(0).'" x2="'.uhel2x(sector2uhel($sondaret['cil'])).'" y2="'.uhel2y(sector2uhel($sondaret['cil'])).'" style="stroke:rgb(009,241,189);stroke-width:5" stroke-opacity="0.5" />';
  smallhexagon('#00aeff',$sondaret['id'],
    uhel2x(sector2uhel($sondaret['cil']))*$sondaret['progres']/100,
    ((uhel2y(sector2uhel($sondaret['cil']))-uhel2y(0))*$sondaret['progres']/100)+uhel2y(0)
  );

}

if ($renderingLnes) {
  echo '</g>';
}

// interface

foreach($sondyall as $sonda) {

  echo '<g transform="translate('.$sonda['display_x'].','.$sonda['display_y'].')">';
  $label='<text x="-14" y="-6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 35px; color:black">'.'0'.$sonda['id'].'</text><text x="-19" y="12" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 20px; color:black">probe</text>';
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
      $label='<text x="-29" y="0" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; color:black">standby</text>';
      break;
    case 'MIS':
      $color="#00ff00";
      $label='<text x="-29" y="0" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; color:black">mission</text>';
      break;
    case 'RTN':
      $color="#00aeff";
      $label='<text x="-29" y="-6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; color:black">return</text>';
      $label.='<text x="-29" y="6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 17px; color:black">to base</text>';
      break;
    case 'ARM':
      $color="#ff0000";
      $label='<text x="-6" y="-6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; color:black">re</text>';
      $label.='<text x="-29" y="6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 17px; color:black">arming</text>';
      break;
    case 'NIL':
    default:
      $color="#707070";
      $label='<text x="-15" y="-6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 18px; color:black">not</text>';
      $label.='<text x="-18" y="6" style="font-family: Omikron, omikron-webfont, sans-serif; font-size: 17px; color:black">avail</text>';
      break;
  }

  largehexagon($color,$label,0,57);
  echo '</g>';
  smallhexagon($color, $sonda['id'], 512 - 16 + (($sonda['id'] - 3) * 34), 142);
}

?></svg>
<?php
if (isset($_GET['xhr'])) {
  // when through XHR, end here
  exit;
}
?></div></div>

<div class="helpers">
  <canvas id="tempusC" class="hidden invisible"></canvas>
  <canvas id="workerC" class="inv"></canvas>
</div>

<script type="text/javascript">
var canvasFin = document.querySelector('#canvasFin');
var canvasTmp = document.querySelector('#tempusC');
var canvasWrk = document.querySelector('#workerC');
var svg       = document.querySelector('svg');
var ctxFin;
var ctxTmp;
var ctxWrk;

var loadedImgs = function() {
  ctxWrk.clearRect(0,0,1024,768);
  var hasToCopy = canvasFin.parentNode.querySelectorAll('canvas');
  if (hasToCopy && hasToCopy.length > 1) {
    ctxWrk.drawImage(canvasFin, 0, 0);
    canvasWrk.className = 'vis';
  }


  setTimeout(function () {

    ctxTmp.clearRect(0, 0, 1024, 768);

    var lines = svg.getElementsByTagName('line');
    renderLines(ctxTmp, lines, svg);

    var polys = svg.getElementsByTagName('polygon');
    renderPolys(ctxTmp, polys, svg);

    var texts = svg.getElementsByTagName('text');
    renderTexts(ctxTmp, texts, svg);

    var imgs = svg.getElementsByTagName('image');
    renderImages(ctxTmp, imgs, svg);

    ctxFin.clearRect(0, 0, 1024, 768);
    ctxFin.drawImage(canvasTmp, 0, 0);

    setTimeout(function () {
      canvasWrk.className = 'inv';
      setTimeout(restart_glitch, 50);
      ctxWrk.drawImage(canvasTmp, 0, 0);
    }, 150);

  }, 150);

};

var setupQueries = function () {
  svg = document.querySelector('svg');
  ctxFin = canvasFin.getContext('2d')
  ctxTmp = canvasTmp.getContext('2d')
  ctxWrk = canvasWrk.getContext('2d')
}

var countImg = 0;
var finalImg = 0;

var prepareImages = function () {
  var imgs = svg.getElementsByTagName('image');
  countImg = 0;
  finalImg = imgs.length;
  $('img.destroyable').remove();
  for (var i = 0; i < finalImg; i++) {
    var nimg = document.createElement('img');
    var pos = getPosition(svg, imgs[i]);
    nimg.style.left = (1*pos.x - 31) + 'px';
    nimg.style.top  = (1*pos.y - 35) + 'px';
    nimg.setAttribute('class', 'tt destroyable ' + imgs[i].getAttribute('xlink:href').replace(/[^a-zA-Z0-9]/g,''));
    nimg.onload = function (e) {
      countImg++;
      e.target.onload = undefined;
      if (countImg >= finalImg) {
        loadedImgs();
      }
    };
    canvasFin.parentNode.insertBefore(nimg, canvasFin.nextSibling);
    nimg.src = imgs[i].getAttribute('xlink:href');
  }
};

var cycle = null;

var cycleRoller = function () {
  clearInterval(cycle);
  cycle = setInterval(initMovie, 10000);
  initMovie();
};

var initMovie = function () {
  if (!started) {
    setupQueries();
    prepareImages();
  }
  else {
    $.ajax({
      url: 'movie.php?xhr=1',
      data: {
        xhr: 1,
        lastId: svg.getAttribute('id')
      },
      dataType: 'text',
      timeout: 5000,
      error: function () {
        setTimeout(cycleRoller, 2000);
      },
      success: function (responseText) {
        if (responseText != '0' && responseText != '1' && responseText != '2') {
          var $el = $('svg:first');
          $el.after($(responseText));
          $el.remove();
          setupQueries();
          prepareImages();
        }
        else {
        }
      }
    });
  }
};

canvasWrk.width  = canvasFin.width  = canvasTmp.width  = svg.getAttribute('width') * 1;
canvasWrk.height = canvasFin.height = canvasTmp.height = svg.getAttribute('height') * 1;

window.onload = cycleRoller;

</script>

</body>
</html>
