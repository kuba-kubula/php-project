<?php
header("Content-type: text/html;charset=utf-8");

if (!isset($_GET['id'])) {

?>
Nebylo nastaveno id terminalu.<br />
Vyberte nize:<br />
<ul>
  <li>
    <p>Terminal 1</p>
    <ol>
      <li><a href="terminal.php?id=1">Terminal 1 800x600</a></li>
      <li><a href="terminal.php?id=1&amp;w=1024">Terminal Ilegalni 1024&times;768</a></li>
    </ol>
  </li>
  <li>
    <p>Terminal 2</p>
    <ol>
      <li><a href="terminal.php?id=2">Terminal 2 800x600</a></li>
      <li><a href="terminal.php?id=2&amp;w=1024">Terminal 2 1024&times;768</a></li>
    </ol>
  </li>
  <li>
    <p>Terminal 3</p>
    <ol>
      <li><a href="terminal.php?id=3">Terminal 3 800x600</a></li>
      <li><a href="terminal.php?id=3&amp;w=1024">Terminal 3 1024&times;768</a></li>
    </ol>
  </li>
  <li style="position:absolute;bottom:10px;left:auto;">
    <p>Terminal Illegal</p>
    <ol>
      <li><a href="terminal.php?id=0">Terminal Ilegalni 800x600</a></li>
      <li><a href="terminal.php?id=0&amp;w=1024">Terminal Ilegalni 1024&times;768</a></li>
    </ol>
  </li>
</ul>
<?php
  exit;
}

?><!doctype html>
<html>
<head>
  <meta name="charset" content="utf-8" />
  <title>Terminal</title>
  <link rel="stylesheet" href="strepiny.css" />
  <script src="jquery.min.js"></script>
  <script src="settings.js"></script>
  <script src="terminal.js"></script>
<style>

</style>
</head>
<body class="terminal">
<div id="main">
  <div id="environment">
    <div id="termbg" class="online"></div>
    <div id="inputLine"></div>
    <div id="dta"><div></div></div>
    <div id="keyboard"></div>
    <div id="cursor"><span></span></div>
  </div>
</div>
<script type="text/javascript">
  document.addEventListener( 'keydown', onDocumentKeyDown, false );
</script>
<script>
createButtons();

data        = new DataClass();
environment = new EnvironmentClass();
http        = new AjaxClass();

environment.data = data;
environment.http = http;

data.http        = http;
data.environment = environment;

http.data        = data;
http.environment = environment;

data.start();
http.start();
environment.start();

environment.setFont(settings.defaultFont);

window.oncontextmenu = function(event) {
  event.preventDefault();
  event.stopPropagation();
  return false;
};

</script>
</body>
</html>