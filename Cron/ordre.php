<?php
/**
 * Example usage of the KrakenAPIClient library. 
 *
 * See https://www.kraken.com/help/api for more info.
 *
 */
chdir("..");
require_once 'class/kraken_class.php';
include_once "class/connexion_class.php";
include_once "class/trader_class.php";
include_once "fonction/vrac.php";
try {


// your api credentials

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
$kraken = $tra->stock_ordre_kraken();
	}
	catch (Exception $e) {
		echo 'API de merde : ',  $e->getMessage(), "\n";
	}
}






echo "FAIT";


} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}




?>