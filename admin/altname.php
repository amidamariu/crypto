<?php
/**
 * Example usage of the KrakenAPIClient library. 
 *
 * See https://www.kraken.com/help/api for more info.
 *
 */
chdir('..');
require_once 'class/connexion_class.php'; 
require_once 'class/trader_class.php'; 

$tra = new trader(1);
$kraken = $tra->get_kraken();
$data = $kraken->QueryPublic('AssetPairs');

$bdd = Connexion::bdd();

$req = $bdd->prepare('INSERT INTO `kraken_pair` (`pair`, `altname`,  `monnaie1`, `monnaie2`)
VALUES(:pair, :altname, :monnaie1, :monnaie2)');


foreach ($data['result'] as $key => $value)
{	
	$req->execute(array(
			'pair' => $key,
			'altname' => $value['altname'],
			'monnaie1' => $value['base'],
			'monnaie2' => $value['quote']
	)) or die(print_r($req->errorInfo()));

	
	
}
?>