<?php
  require_once("_settings/settings.php");
  if (!function_exists("json_encode")) {
    require_once "_settings/json.php";
  }

  if (isset($_GET['last'])) {
    $last = (int)$_GET['last'];
    $events = $db->fetchAssoc("SELECT id, data FROM strepiny_log WHERE id > $last ORDER BY id ASC LIMIT 0,30");
    if ($events) {
      echo json_encode($events);
    }
    else {
      echo json_encode(array());
    }
    exit;
  }
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="strepiny.css" />
<meta http-equiv="refresh" content="600;URL='events.php">
<meta charset="UTF-8">
<script type="text/javascript" src="jquery.min.js"></script>
</head>
<body>
  <div id="eventwrapper">
<?php
$events = $db->fetchAssoc('SELECT * FROM strepiny_log ORDER BY id DESC LIMIT 0,30');
echo mysql_error();
$lastId = 0;
foreach ($events as $event) {
	echo '<div class="panel">';
	echo $event['data'];
	echo '</div>';
  if ($event['id'] > $lastId) {
    $lastId = $event['id'];
  }
}

?>
<script>
$(document).ready(function () {
  var lastId = <?php echo (int)$lastId; ?>;
  var $eventWrapper = $('#eventwrapper');
  var getNewEvents = function() {
    $('body').addClass('loading');
    $.ajax({
      url: 'events.php',
      type: 'GET',
      dataType: 'json',
      timeout: 4000,
      data: {
        last: lastId
      },
      error: function () {
      },
      success: function (data) {
        if (data && data.length > 0) {
          var lastLastId = lastId;
          $.each(data, function(i, item) {
            $eventWrapper.prepend($('<div class="panel"></div>').text(item.data));
            if (item.id > lastLastId) {
              lastLastId = item.id;
            }
          });
          if (lastLastId > lastId) {
            lastId = lastLastId;
          }
        }
      },
      complete: function () {
        $('body').removeClass('loading');
        setTimeout(getNewEvents, 6000);
      }
    });
  }
  setTimeout(getNewEvents, 6000);
});
</script>
  </div>
</body>
</html>