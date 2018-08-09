<?php
/**
 * Example usage of the KrakenAPIClient library.
 *
 * See https://www.kraken.com/help/api for more info.
 *
 */
chdir("..");
require_once 'class/kraken_class.php';
require_once 'class/connexion_class.php';
require_once 'class/trader_class.php';
require_once 'class/bitrex_class.php';
require_once 'fonction/vrac.php';


set_time_limit(3000); 




$bdd = Connexion::bdd();

$sql = "SELECT * FROM `trader` WHERE 1";
$req = $bdd->query($sql);




/*

$sql = "TRUNCATE ordre";
$bdd->query($sql);
$sql = "TRUNCATE remiordre";
$bdd->query($sql);
$sql = "TRUNCATE altname";
$bdd->query($sql);
*/



	
	try {
		
	
		$tra = new trader(1);
		
	$kraken=$tra->get_kraken();
	
$pf = trader::get_pfs_kraken();

	foreach ($pf as  $one){

		try {
			$key = $one['monnaie'];
		
		if($key != 'ZUSD' && $key != 'ZEUR')
		{
			$pair =	KrakenAPI::get_pair($key,'ZUSD');

			$res2 = $kraken->QueryPublic('Ticker', array('pair' => $pair ));
			$prix = $res2['result'][$pair]['c'][0];
			$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('".$pair."',".$prix.",'kraken')";
		}
		else
		{
			if($key == 'ZEUR')
			{
			$prix = 1.17;
			$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('ZEUR',".$prix.",'kraken')";
			}
			else
			{
			$prix = 1;
			$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('ZUSD',".$prix.",'kraken')";
			}
		}

		$bdd->query($sql);
		echo "ok".$key."<br>";
		
		
		
		
		
		
		if($key != 'XXBT' && $key != 'ZUSD')
		{
			$pair =	KrakenAPI::get_pair($key,'XXBT');
			$res2 = $kraken->QueryPublic('Ticker', array('pair' => $pair ));
			$prix = $res2['result'][$pair]['c'][0];
			$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('".$pair."',".$prix.",'kraken')";
		}
		else
		{
			$prix = 1;
			$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('ZUSD'".$prix.",'kraken')";
		}
		
		$bdd->query($sql);
		echo "ok".$key."<br>";
		
		
		}
		catch (Exception $e)
		{
			echo "API de merde".$key."<br>";
		}
	}
	} catch (Exception $e) {
	echo 'Exception reçue : ',  $e->getMessage(), "\n";
}





?>