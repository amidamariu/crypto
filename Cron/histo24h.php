<?php
chdir("..");
require_once 'class/binance_class.php';
require_once 'class/trader_class.php';
require_once 'fonction/vrac.php';


$bdd = Connexion::bdd();

$sql = "SELECT * FROM `trader` WHERE 1";
$req = $bdd->query($sql);

$sql = "DELETE FROM `24h` WHERE date < DATE_SUB(NOW(), INTERVAL 1 DAY)";
$bdd->query($sql);

foreach($req->fetchAll() as $trader)
{
	$tra = new trader($trader['id']);

	
	try {
		
$valeur = $tra->get_total();
$last_checkpoint = $tra->get_debut_mois();
$sql = 'INSERT INTO `24h` (`montant`, `trader`) VALUES('.$valeur.','.$trader['id'].')';
$req = $bdd->query($sql);

	}
	catch (Exception $e) {
		echo 'Exception reçue : ',  $e->getMessage(), "\n";
	}
	
	

	
}



?>


	