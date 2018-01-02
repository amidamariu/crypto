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

foreach($req->fetchAll() as $trader)
{
	
	try {
		
		
		$tra = new trader($trader['id']);
		
	
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