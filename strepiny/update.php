<?php

	require_once("_settings/settings.php");
	require_once("svg/moloch.php");

	if (!function_exists("json_encode")) {
		require_once "_settings/json.php";
	}

$nestabilita = $db->fetch1Assoc('SELECT * FROM strepiny_nestabilita WHERE id = "1";');
$systemy     = $db->fetchAssoc('SELECT * FROM strepiny_systemy WHERE spusten = "Y" ORDER BY id ASC');
$sondyAll    = $db->fetchAssoc('SELECT * FROM strepiny_sondy ORDER BY id ASC');

$sondyStats = array();
$sondyarm = array();
$sondymis = array();
$sondyret = array();

foreach ($sondyAll as $sonda) {
	$sondyStats[$sonda['id']] = $sonda;
	if ($sonda['stav'] == 'ARM') {
		$sondyarm[] = $sonda;
	}
	elseif ($sonda['stav'] == 'MIS') {
		$sondymis[] = $sonda;
	}
	elseif ($sonda['stav'] == 'RTN') {
		$sondyret[] = $sonda;
	}
}


$nestabilita['dnc'] = -$nestabilita['mo'];
foreach ($systemy as $system) {
	$nestabilita['dnc'] += $system['no'];
}

// prezbrojeni (ARM) sondy
$updates = array();
foreach ($sondyarm as $sondaarm) {
	$sonda = $sondaarm;
	$sonda['progres'] += $sondacfg['armspeed'];
	if ($sonda['progres'] >= 100) {
		$sonda['progres'] = 0;
		$sonda['stav'] = 'HOM';
		$db->update('strepiny_sondy',array('stav'=>'HOM', 'progres' => 0),'WHERE id="'.$sonda['id'].'"');
		$db->insert('strepiny_log',array('script'=>'update.php','data'=>'Sonda '.$sonda['id'].' má přezbrojeno'));
	} else {
		$updates[] = $sonda['id'];
	}
	$sonda['progres'] = round($sonda['progres']);
	$sondyStats[$sonda['id']] = $sonda;
	$nestabilita['dnc'] += $sondacfg['armno'];
}
if (count($updates) > 0) {
	$db->execute("UPDATE ".$db->prefix."strepiny_sondy SET progres = (progres + ".round($sondacfg['armspeed']).") WHERE id IN ('".join("','", $updates)."');");
}

// odlet (MIS) sondy a pripadny update na navrat (RTN) sondy
$updates = array();
foreach ($sondymis as $sondamis) {
	$sonda = $sondamis;
	$sonda['progres'] += $sondacfg['misspeedmin'] + (($sondacfg['misspeedmax'] - $sondacfg['misspeedmin']) * sectordistance($sonda['cil']));

	if ($sonda['progres'] >= 100) {
		$sonda['stav'] = 'RTN';
		$sonda['progres'] = '100';
		$db->update('strepiny_sondy',array('stav'=>'RTN','progres'=>'100'),'WHERE id="'.$sonda['id'].'"');
		$db->insert('strepiny_log',array('script'=>'update.php','data'=>'Probíhá objevení sektoru '.$sonda['cil'].' sondou '.$sonda['id']));

		if ($molochSektor = $db->fetch1Assoc('SELECT * FROM strepiny_moloch WHERE cislo="'.$sonda['cil'].'"')) {
			if (($sonda['slot1'] == $molochSektor['prekazka']) || ($sonda['slot2'] == $molochSektor['prekazka']) || ($molochSektor['prekazka'] == 'EMPT')) {
				if (($sonda['slot1'] == $molochSektor['zarizeni']) || ($sonda['slot2'] == $molochSektor['zarizeni'])) {
					$db->insert('strepiny_log',array('script'=>'update.php','data'=>'Sonda vyzvedla sektor '.$sonda['cil']));
					$db->insert('strepiny_db_obsah',array('klic'=>$sonda['cil'],'obsah'=>$molochSektor['desc_vyzvednuto'],'priorita'=>'3','odemceni'=>'START'));
					$db->insert('strepiny_db_udalosti',array('kod'=>$sonda['cil'],'nazev'=>'Objeven sektor '.$sonda['cil'],'probehla'=>'A'));
				} else {
					$db->insert('strepiny_log',array('script'=>'update.php','data'=>'Sonda navstivila sektor '.$sonda['cil'].' ale nevyzvedla'));
					$db->insert('strepiny_db_obsah',array('klic'=>$sonda['cil'],'obsah'=>$molochSektor['desc_zarizeni'],'priorita'=>'2','odemceni'=>'START'));
				}
			} else {
				$db->insert('strepiny_log',array('script'=>'update.php','data'=>'Sonda navstivila sektor '.$sonda['cil'].' ale neprosla prekazkou'));
				$db->insert('strepiny_db_obsah',array('klic'=>$sonda['cil'],'obsah'=>$molochSektor['desc_prekazka'],'priorita'=>'1','odemceni'=>'START'));
			}
		} else {
			$db->insert('strepiny_log',array('script'=>'update.php','data'=>'Generuju fiktivni zaznam o '.$sonda['cil']));
			$db->insert('strepiny_db_obsah',array('klic'=>$sonda['cil'],'obsah'=>'Navzdory dukladnemu pruzkumu sonda nedokazala v danem sektoru nadetekovat nic zajimaveho','priorita'=>'1','odemceni'=>'START'));
		}
	} else {
		$db->update('strepiny_sondy', array('progres' => round($sonda['progres'])), "WHERE id = ".$sonda['id']);
	}
	$nestabilita['dnc'] += $sondacfg['misno'];
	$sonda['progres'] = round($sonda['progres']);
	$sondyStats[$sonda['id']] = $sonda;
}

// RTN sondy
foreach($sondyret as $sondaret) {
	$sonda = $sondaret;
	$sonda['progres'] -= ($sondacfg['misspeedmin'] + (($sondacfg['misspeedmax'] - $sondacfg['misspeedmin']) * sectordistance($sonda['cil'])));

	if ($sonda['progres'] <= 0) {
		$sonda['stav'] = 'HOM';
		$sonda['progres'] = 0;
		$db->update('strepiny_sondy',array('stav'=>'HOM', 'progres' => 0),'WHERE id="'.$sonda['id'].'"');
		$db->insert('strepiny_log',array('script'=>'update.php','data'=>'Ze sektoru '.$sonda['cil'].' se vrátila sonda '.$sonda['id']));
	} else {
		$db->update('strepiny_sondy', array('progres' => round($sonda['progres'])), "WHERE id = ".$sonda['id']);
	}
	$nestabilita['dnc'] += $sondacfg['misno'];
	$sonda['progres'] = round($sonda['progres']);
	$sondyStats[$sonda['id']] = $sonda;
}

if ($_GET["robot"] == 'true') {
	$nestabilita['nc'] += round($nestabilita['dnc'] / 6);
	if ($nestabilita['nc'] < 0) {
		$nestabilita['nc'] = 0;
		$nestabilita['vyrazeno'] = 'N';
		$db->update('strepiny_systemy', array('spusten'=>'Y'), 'WHERE (id="6") OR (id="0")');
	}
	if ($nestabilita['nc'] > $nestabilita['ms']) {
		$nestabilita['vyrazeno'] = 'Y';
		$db->update('strepiny_systemy',array('spusten'=>'N'),'');
	}
}

$text = "";

if ($nestabilita['vyrazeno'] == 'Y') {
	$audio = 0;
	$lights = 0;
	$reverse = 1;
	$pulsespeed = 3.8;
	$rotationspeed = 50;
	$r = 1;
	$g = 0.19;
	$b = 0;
	$text += 'Audio Jadro vyrazeno:'.$video.' svetla vypnuta:'.$lights;
} else {

	$reverse = ($nestabilita['dnc'] < 0) ? 1 : 0;
	$pulsespeed = ($nestabilita['dnc'] > 100) ? 1 : (3.8 - ((($nestabilita['dnc'] + 100) / 200) * (3.8 - 1)));

	if ($nestabilita['nc'] > 1200) {
		$rotationspeed = 5;
		$r = 1;
		$g = 0.19;
		$b = 0;
	} else {
		$rotationspeed = 50 - (($nestabilita['nc'] / 1200) * (50 - 5));

		$r1 = 1;
		$g1 = 0.99;
		$b1 = 0.25;

		$r2 = 0.27;
		$g2 = 0.19;
		$b2 = 0;

		$r = 0.27 + ($nestabilita['nc'] / 1200) * ($r1 - $r2);
		$g = 0.99 - ($nestabilita['nc'] / 1200) * ($g1 - $g2);
		$b = 0 + ($nestabilita['nc'] / 1200) * ($b1 - $b2);

	}

	$text = '<br/>';
	if ($nestabilita['nc'] > 500) {
		$text += 'Audio Jadro nestabilni, ';
		if ($nestabilita['dnc'] > 0) {
			$audio = 4;
			$text += 'zatez vysoka:'.$audio;
		} else {
			$audio = 3;
			$text += 'zatez nizka:'.$audio;
		}
	}
	else {
		$text += 'Audio Jadro stabilni, ';
		if ($nestabilita['dnc'] > 0) {
			$audio = 2;
			$text += 'zatez vysoka:'.$audio;
		} else {
			$audio = 1;
			$text += 'zatez nizka:'.$audio;
		}
	}

	if ($nestabilita['dnc'] > 200) {
		$lights = 2;
		$text += 'Svetla blikaji:'.$lights;
	}
	else {
		$lights = 1;
		$text += 'Svetla sviti:'.$lights;
	}
}

$db->update('strepiny_nestabilita', array(
	'nc' => $nestabilita['nc'],
	'dnc' => $nestabilita['dnc'],
	'vyrazeno' => $nestabilita['vyrazeno'],
	'lastvideo' => $video,
	'lastlights'=>$lights
), 'WHERE id="1";');

function sterilize($item) {
	$keys = array_keys($item);
	$keys = array_flip(array_filter($keys, 'clean_numeric_keys'));
	return array_intersect_key($item, $keys);
}
function clean_numeric_keys($key) {
	return !is_numeric($key);
}

$sondyStats = array_map('sterilize', $sondyStats);
$sondyStats = array_values($sondyStats);
ksort($sondyStats);

// Volam VVVcka

?><!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="strepiny.css" />
<?php echo ($_GET["robot"]=='true')?'<meta http-equiv="refresh" content="'.$robotTime.'">':''; ?>
<meta charset="UTF-8" />
</head>
<body>
<script type="text/javascript">
if (window.parent) {
	try {
		window.parent.callUpdates('<?php echo json_encode($sondyStats); ?>');
	}
	catch (e) {
	}
}
</script>

<div class="panel" id="celkova">
	Celkova nestabilita: <strong><?php echo $nestabilita['nc'];?></strong>
</div>
<div class="panel" id="zmena">
	Poslední změna nestability: <strong><?php echo $nestabilita['dnc'];?></strong>
</div>
	Vysílám projekci:
<?php
	flush();
	if (($nestabilita['lastvideo'] != $video) || ($nestabilita['lastlights'] != $lights)) {
		$url = 'http://10.0.0.3:81/wwwebControl?audio='.$audio.'&lights='.$lights.'&pulsespeed='.round($pulsespeed,3).'&rotationspeed='.round($rotationspeed,3).'&r='.round($r,3).'&g='.round($g,3).'&b='.round($b,3).'&reverse='.$reverse;
		echo $url;
		// do not load vvvv at my Mac, baby. And DO NOT do it at Heroku, sir!
		if (stripos(PHP_OS, 'Darwin') === false && !isset($_SERVER["CLEARDB_DATABASE_URL"])) {
			@file_get_contents($url);
		}
	}


?>
</body>
</html>

