<!DOCTYPE html>
<?php

require_once("_settings/settings.php");

$go = isset($_GET['go']) ? $_GET['go'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

$system = $db->fetch1Assoc('SELECT * FROM strepiny_systemy WHERE id="'.$id.'";');

if ($system) {

  if ($go == 'START') {
  	$db->update('strepiny_systemy',array('spusten'=>'Y'),'WHERE id="'.$id.'";');
  	$system['spusten'] = 'Y';
  	$nestabilita = $db->fetch1Assoc('SELECT * FROM strepiny_nestabilita WHERE id="1";');
  	$nestabilita['nc'] += $system['ns'];
  	$db->update('strepiny_nestabilita',array('nc'=>$nestabilita['nc']),'WHERE id="1";');
  }
  if ($go == 'STOP') {
  	$db->update('strepiny_systemy',array('spusten'=>'N'),'WHERE id="'.$id.'";');
  	$system['spusten'] = 'N';
  }

}
else {
  die("Chyba. System nenalezen.");
  exit;
}



?>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="strepiny.css" />
  <meta http-equiv="refresh" content="10;URL='system.php?id=<?php echo $id; ?>'">
  <meta charset="UTF-8">
</head>
<body>
  <div class="<?php echo $system['spusten']; ?>">
  <div class="panel" id="nazev">
    Název systému: <strong><?php echo $system['nazev'];?></strong>
  </div>
  <div class="panel" id="parametry">
    Průběžná nestabilita <strong><?php echo $system['no'];?></strong><br/>
    Spouštěcí nestabilita <strong><?php echo $system['ns'];?></strong>
  </div>
  <div class="panel" id="ovladani">
   <form action="system.php" method="GET">
     V provozu:<strong><?php echo ($system['spusten']== 'Y') ? 'ANO' : 'NE'; ?></strong>
     <input type="submit" value="<?php echo ($system['spusten'] == 'Y') ? 'STOP' : 'START'; ?>" name="go" />
     <input type="hidden" value="<?php echo $id; ?>" name="id" />
   </form>
  </div>
 </div>
</body>
</html>