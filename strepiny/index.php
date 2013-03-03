<?php
require_once("_settings/settings.php");
$nestabilita = $db->fetch1Assoc('SELECT * FROM '.$db->prefix.'strepiny_nestabilita WHERE id="1";');
header("content-type:text/html");
?>
<!doctype html>
<html>
<head>
  <title>Střepiny - orží kontrolní panel</title>
  <meta charset="UTF-8" />
  <link rel="stylesheet" type="text/css" href="strepiny.css" />
  <script src="jquery.min.js"></script>
</head>
<body>
  <h1>Střepiny - orží kontrolní panel</h1>
  <div class="panel" id="systems">
   <iframe class="system" src="system.php?id=0" scrolling="no"></iframe>
   <iframe class="system" src="system.php?id=1" scrolling="no"></iframe>
   <iframe class="system" src="system.php?id=2" scrolling="no"></iframe>
   <iframe class="system" src="system.php?id=3" scrolling="no"></iframe>
   <iframe class="system" src="system.php?id=4" scrolling="no"></iframe>
   <iframe class="system" src="system.php?id=5" scrolling="no"></iframe>
   <iframe class="system" src="system.php?id=6" scrolling="no"></iframe>
 </div>
 <div class="panel" id="sondy">
   <iframe name="sonda1" id="sonda1" class="sonda" src="sonda.php?id=1" scrolling="no"></iframe>
   <iframe name="sonda2" id="sonda2" class="sonda" src="sonda.php?id=2" scrolling="no"></iframe>
   <iframe name="sonda3" id="sonda3" class="sonda" src="sonda.php?id=3" scrolling="no"></iframe>
   <iframe name="sonda4" id="sonda4" class="sonda" src="sonda.php?id=4" scrolling="no"></iframe>
   <iframe name="sonda5" id="sonda5" class="sonda" src="sonda.php?id=5" scrolling="no"></iframe>
   <iframe name="sonda6" id="sonda6" class="sonda" src="sonda.php?id=6" scrolling="no"></iframe>
   <iframe class="system" src="system.php?id=7">
   </iframe>

  </div>
  <div class="panel" id="output">
   <iframe src="update.php?robot=true"></iframe>
  </div>
  <div class="panel" id="events">
    <iframe src="events.php" class="big"></iframe>
    <div class="updateVars">
      <?php
        $overrides = array('glitch' => "Glitch", 'nc' => "Nestabilita", 'ending' => 'Ending');
        foreach($overrides as $k => $v) :
      ?>
       <form action="updateVariable.php" method="post" class="var_<?= $k; ?>">
         <span><?= $v; ?>:</span> <input type="hidden" name="varname" value="<?= $k; ?>" />
         <input class="inn" size="2" type="text" name="varvalue" value="<?= $nestabilita[$k] ?>" />
         <button type="submit">Update <?= $k; ?></button>
       </form>
      <?php endforeach; ?>
    </div>
  </div>

<script type="text/javascript">
var runForFirst = true;
var updateNc = function(nc) {
  nc = JSON.parse(nc);
  $.each(nc, function (key, val) {
    if (isNaN(key)) {
      var $el = $('form.var_' + key);
      if ($el && $el.length > 0) {
        $el.find('input[name="varvalue"]').val(val);
      }
    }
  });
};

var callUpdates = function(data) {
  if (runForFirst) {
    runForFirst = false;
    return true;
  }
  data = JSON.parse(data);
  for (var ii = 0, len = data.length; ii < len; ii++) {
    var d = data[ii];
    var f;
    var k = 1 + Math.round(ii * 1);
    try{
      f = $('#sonda' + k).contents();
      f.find('#goButton' + k).val(d.stav == 'MIS' ? 'Navrat' : 'Vypustit').attr('disabled', d.stav == 'ARM' || d.stav == 'RTN');
      f.find('#stavSonda' + k).text(d.stav);
      f.find('#progresSonda' + k).text(d.progres);
      f.find('#mainStavSonda' + k).attr('class', d.stav);
      f.find('#armBtn' + k).attr('disabled', d.stav != 'HOM');
    }
    catch (e) {
    }
  };
}

$('.updateVars form').submit(function(e){
  e.preventDefault();
  e.stopPropagation();
  e = $(this);
  e.addClass('sending');
  $.ajax({
    url: e.attr('action'),
    type: 'POST',
    timeout: 3000,
    dataType: 'text',
    data: e.serialize(),
    complete: function () {
      e.removeClass('sending');
    }
  });
});
</script>
</body>
</html>