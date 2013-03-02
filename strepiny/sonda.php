<?php

  $cil = isset($_GET['cil']) ? $_GET['cil'] : '';
  $id = isset($_GET['id']) ? $_GET['id'] : '';
  $go = isset($_GET['go']) ? $_GET['go'] : '';
  $slot1 = isset($_GET['slot1']) ? $_GET['slot1'] : '';
  $slot2 = isset($_GET['slot2']) ? $_GET['slot2'] : '';

require_once("_settings/settings.php");

if ($go == 'Vypustit') {
  $db->update('strepiny_sondy',array('stav'=>'MIS','progres'=>0,'cil'=>$cil),'WHERE id="'.$id.'";');
  $db->insert('strepiny_log',array('script'=>'sonda.php','data'=>'Vyslana sonda '.$id.' do sektoru '.$cil));
  header("Location: sonda.php?id=".$id);
  exit;
}

if ($go == 'Navrat') {
  $db->update('strepiny_sondy',array('stav'=>'RTN'),'WHERE id="'.$id.'";');
  $db->insert('strepiny_log',array('script'=>'sonda.php','data'=>'Odvolana sonda '.$id.' ze sektoru '.$cil));
  header("Location: sonda.php?id=".$id);
  exit;
}

if ($go == 'Rearm') {
  $sonda = $db->fetch1Assoc('SELECT * FROM strepiny_sondy WHERE id="'.$id.'";');
  if ($sonda['slot1'] != $slot1 || $sonda['slot2'] != $slot2) {
    $db->update('strepiny_sondy',array('stav'=>'ARM','slot1'=>$slot1,'slot2'=>$slot2,'progres'=>0),'WHERE id="'.$id.'";');
    $db->insert('strepiny_log',array('script'=>'sonda.php','data'=>'Sonda '.$id.' prezbrojovana na '.$slot1.' + '.$slot2));
  }
  header("Location: sonda.php?id=".$id);
  exit;
}

$sonda=$db->fetch1Assoc('SELECT * FROM strepiny_sondy WHERE id="'.$id.'";');

$slots = array(
  'NIL' => '- NIL -',
  'RADI' => 'Radštít',
  'SDBR' => 'Laser',
  'BDBR' => 'AIpilot',
  'FIRE' => 'Chlazení',
  'TURR' => 'Emp dělo',
  'ARMR' => 'Plasvrták',
  'COMP' => 'Interf',
  'CORR' => 'Oprav',
  'EXTR' => 'Úchyty'
);

?><!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="refresh" content="600;URL='sonda.php?id=<?php echo $_GET['id']; ?>'" />
  <link rel="stylesheet" type="text/css" href="strepiny.css" />
  <script src="jquery.min.js"></script>
</head>
<body>
  <form action="sonda.php" method="GET">
  <div id="mainStavSonda<?php echo $sonda['id']; ?>" class="<?php echo $sonda['stav']; ?>">
  <div class="panel">
  Číslo sondy: <strong><?php echo $sonda['id'];?></strong>
  </div>
  <div class="panel">
    Slot 1:<select id="slot1Sonda<?php echo $sonda['id']; ?>" name="slot1">
      <?php foreach ($slots as $key => $value) : ?>
        <option value="<?php echo $key; ?>" <?php if ($sonda['slot1'] == $key) { echo 'selected';}?>><?php echo $value ?></option>
      <?php endforeach; ?>
    </select>
    Slot 2:<select id="slot2Sonda<?php echo $sonda['id']; ?>" name="slot2">
      <?php foreach ($slots as $key => $value) : ?>
        <option value="<?php echo $key; ?>" <?php if ($sonda['slot2'] == $key) { echo 'selected';}?>><?php echo $value ?></option>
      <?php endforeach; ?>
   </select>
   <input id="armBtn<?php echo $sonda['id']; ?>" type="submit" name="go" value="Rearm" <?php echo ($sonda['stav'] == 'HOM') ? '' : 'disabled="disabled"';?>/>
  </div>
  <div class="panel">
   Cíl mise: <input type="text" name="cil" value="<?php echo $sonda['cil']; ?>">
   <input id="goButton<?php echo $sonda['id']; ?>" type="submit" name="go" value="<?php echo ($sonda['stav'] == 'MIS') ? 'Navrat' : 'Vypustit'; ?>" <?php echo (($sonda['stav'] == 'ARM') || ($sonda['stav'] == 'RTN')) ? 'disabled="disabled"' : '';?>/>
   V provozu:<strong id="stavSonda<?php echo $sonda['id']; ?>"><?php echo ($sonda['stav']); ?></strong>
   Progres: <strong id="progresSonda<?php echo $sonda['id']; ?>"><?php echo ($sonda['progres']); ?></strong>
  </div>

   <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id"/>
   </form>
  </div>
  </div>
</body>
</html>

