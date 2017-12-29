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

$bdd = Connexion::bdd();

for ($id = 1; $id <= 4; $id++) 
{
try {
	


	
	
	$tra = new trader($id);
	$kraken=$tra->get_kraken();
	
$pf = $tra->get_pf_kraken();

	foreach ($pf as  $one){

		try {
			$key = $one['monnaie'];
		
		if($key != 'ZEUR')
		{
			$pair =	KrakenAPI::get_pair($key,'ZEUR');
			$res2 = $kraken->QueryPublic('Ticker', array('pair' => $pair ));
			$prix = $res2['result'][$pair]['c'][0];
			$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('".$pair."',".$prix.",'kraken')";
		}
		else
		{
			$prix = 1;
			$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('ZEUR'".$prix.",'kraken')";
		}

		$bdd->query($sql);
		echo "ok".$key."<br>";
		
		
		
		
		
		
		if($key != 'XXBT' && $key != 'ZEUR')
		{
			$pair =	KrakenAPI::get_pair($key,'XXBT');
			$res2 = $kraken->QueryPublic('Ticker', array('pair' => $pair ));
			$prix = $res2['result'][$pair]['c'][0];
			$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('".$pair."',".$prix.",'kraken')";
		}
		else
		{
			$prix = 1;
			$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('ZEUR'".$prix.",'kraken')";
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



}


for ($id = 1; $id <= 4; $id++)
{
	try {
		
		
		
		
		
		$tra = new trader($id);
		$binance=$tra->get_binance();
		
		$pf = $tra->get_pf_binance();

		$price = $binance->prices();

		foreach ($pf as  $one){
			
			try {
				$key = $one['monnaie'];

				if($one['monnaie'] != 'BTC')
				{
					$prixBTC = $price[$key.'BTC'];
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
	} catch (Exception $e) {
		echo 'Exception reçue : ',  $e->getMessage(), "\n";
	}
	
}
	
	

for ($id = 1; $id <= 3; $id++)
{
	try {
		
		
		
		
		
		$tra = new trader($id);
		$bitrex=$tra->get_bitrex();
		$pf = $tra->get_pf_bitrex();
		
		
		foreach ($pf as  $one){
			
			try {
				$key = $one['monnaie'];
				
				if($one['monnaie'] != 'BTC')
				{
					$prixBTC = $bitrex->GetTicker("BTC-".$one['monnaie'])->result->Last;
					
					$sql = "REPLACE INTO price (monnaie,prix,plateforme) VALUES ('BTC-".$key."',".$prixBTC.",'bitrex')";
					$bdd->query($sql);
					echo "ok".$key."<br>";
				}
			}
			
			catch (Exception $e)
			{
				echo "API de merde".$key."<br>";
			}
		}
	} catch (Exception $e) {
		echo 'Exception reçue : ',  $e->getMessage(), "\n";
	}
	
}









?>