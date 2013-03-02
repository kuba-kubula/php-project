<!DOCTYPE html>
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
   <iframe class="system" src="system.php?id=0">
   </iframe>
   <iframe class="system" src="system.php?id=1">
   </iframe>
   <iframe class="system" src="system.php?id=2">
   </iframe>
   <iframe class="system" src="system.php?id=3">
   </iframe>
   <iframe class="system" src="system.php?id=4">
   </iframe>
   <iframe class="system" src="system.php?id=5">
   </iframe>
   <iframe class="system" src="system.php?id=6">
   </iframe>
 </div>
 <div class="panel" id="sondy">
   <iframe name="sonda1" id="sonda1" class="sonda" src="sonda.php?id=1">
   </iframe>
   <iframe name="sonda2" id="sonda2" class="sonda" src="sonda.php?id=2">
   </iframe>
   <iframe name="sonda3" id="sonda3" class="sonda" src="sonda.php?id=3">
   </iframe>
   <iframe name="sonda4" id="sonda4" class="sonda" src="sonda.php?id=4">
   </iframe>
   <iframe name="sonda5" id="sonda5" class="sonda" src="sonda.php?id=5">
   </iframe>
   <iframe name="sonda6" id="sonda6" class="sonda" src="sonda.php?id=6">
   </iframe>

  </div>
  <div class="panel" id="output">
   <iframe src="update.php?robot=true"></iframe>
  </div>
  <div class="panel" id="events">
   <iframe src="events.php" class="big"></iframe>
  </div>

<script type="text/javascript">
var runForFirst = true;
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
</script>
</body>
</html>