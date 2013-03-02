<?php 
header('Content-Type: text/html; charset=utf-8');


require_once("_settings/settings.php");

$login = isset($_GET[ "login" ]) ? $_GET[ "login" ] : "";
$password = isset($_GET[ "password" ]) ? $_GET[ "password" ] : "";
$klic = isset($_GET['klic']) ? $_GET['klic'] : "";
$term = isset($_GET['term']) ? $_GET['term'] : 0;
//$db->insert('strepiny_log',array('script'=>'databaze.php','data'=>implode(':',$_GET)));


if (($term == 0) || ($uzivatel = $db->fetch1Assoc("SELECT * FROM strepiny_db_uzivatele WHERE (jmeno = \"" . addslashes($login) . "\") AND (heslo=\"" . addslashes($password) . "\")"))) {

	if ($term != 0) {
		$db->insert('strepiny_log', array('script'=>'databaze.php','data'=>'Login: '.$login));
	}

	if ($klic == 'ROLE') {
		echo  'OK:'.$uzivatel["role"];
	}
	else {
		$odpoved = $db->fetch1Assoc('SELECT * FROM strepiny_systemy WHERE id="'.$term.'"');
		if ($odpoved['spusten'] == 'N') {
			echo 'OFF';
		}
		else {
			if ($klic != 'STATUS') {
				$db->insert('strepiny_log', array('script'=>'databaze.php','data' => $login.' provedl dotaz na '.$klic));
			}
			if ($odpoved = $db->fetch1Assoc('SELECT * FROM strepiny_db_obsah WHERE klic="'.addslashes($klic).'" ORDER BY priorita DESC;')) {
				if ($odpoved['odemceni'] == 'START') {
					echo 'OK:'.$odpoved['obsah'];
				}
				else {
					$udalost = $db->fetch1Assoc('SELECT * FROM strepiny_db_udalosti WHERE kod="'.addslashes($odpoved['odemceni']).'";');
					if ($udalost['probehla'] == 'A') {
						echo 'OK:'.$odpoved['obsah'];
					}
					else {
						echo 'CORRUPTED';
					}
				}
			}
			else {
				echo 'NOT FOUND';
			}
		}
	}
}
else {
	if (($login == "MAINTENANCE") && ($password == "INSECURITY")) {
		// $db->insert('strepiny_log',array('script'=>'databaze.php','data'=>'Login: '.$login));

		if ($klic == 'ON') {
			$db->update('strepiny_systemy', array('spusten'=>'Y'),'WHERE id="'.$term.'"');
			echo 'OK: Terminal zapnut';
			// $db->insert('strepiny_log',array('script'=>'databaze.php','data'=>'Terminal '.$term.' zapnut'));
		}

		if ($klic == 'OFF') {
			$db->update('strepiny_systemy',array('spusten'=>'N'),'WHERE id="'.$term.'"');
			echo 'OK: Terminal vypnut';
			// $db->insert('strepiny_log',array('script'=>'databaze.php','data'=>'Terminal '.$term.' vypnut'));
		}

		if ($klic == 'STATUS') {
			$odpoved = $db->fetch1Assoc('SELECT * FROM strepiny_systemy WHERE id="'.$term.'";');
			if ($odpoved['spusten'] == 'N') {
				echo 'OFF';
			}
			else {
				echo 'ON';
			}
		}
		if (!(($klic == 'ON') || ($klic == 'OFF') || ($klic  == 'STATUS'))) {
			echo 'OK: Ukolem udrzby je pouze zapinat a vypinat terminal';
		}

	}
	else {
		echo 'DENIED';
	}
}