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


set_time_limit(300); 


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




foreach($req->fetchAll() as $trader)
{
	
	try {
		

		$tra = new trader($trader['id']);
		
		$binance=$tra->get_binance();
		
		$pf = $tra->get_pf_binance();


		
		$price_bin = $binance->prices();
		
		echo "<pre>";
		var_dump($GLOBALS['price']);
		echo "</pre>";
		
		foreach ($pf as  $one){
			
			try {
				$key = $one['monnaie'];

				if($one['monnaie'] != 'BTC' && $one['monnaie'] != 'USDT')
				{
					$prixBTC = $price_bin[$key.'BTC'];
					$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('".$key."BTC',".$prixBTC.",'binance')";
				$bdd->query($sql);
				echo "ok".$key."<br>";
				}
				if($one['monnaie'] == 'USDT')
				{
					$prixBTC = 1.0/get_prix_sql("XXBTZUSD");
					$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('".$key."BTC',".$prixBTC.",'binance')";
					$bdd->query($sql);
					echo "ok".$key."<br>";
				}
			}
	
			catch (Exception $e)
			{
				echo "API de merde".$key."<br>";
			}
		}
		break;
	} catch (Exception $e) {
		echo 'Exception reçue : ',  $e->getMessage(), "\n";
	}
	
}
	
	

?>