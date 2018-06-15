<?php
chdir("..");
require_once 'class/binance_class.php';
require_once 'class/trader_class.php';
require_once 'fonction/vrac.php';


$bdd = Connexion::bdd();

$sql = "SELECT * FROM `trader` WHERE 1";
$req = $bdd->query($sql);



foreach($req->fetchAll() as $trader)
{
	$tra = new trader($trader['id']);

	
	try {
		
$valeur = $tra->get_total();
$valeur_btc = $tra->get_total_btc();
$sql = 'INSERT INTO `24h` (`montant`,`montantBTC`, `trader`) VALUES('.$valeur.','.$valeur_btc.','.$trader['id'].')';
$req = $bdd->query($sql);

	}
	catch (Exception $e) {
		echo 'Exception reçue : ',  $e->getMessage(), "\n";
	}
	
	

	
}



?>


	