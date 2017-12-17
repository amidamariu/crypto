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

$sql = 'INSERT INTO `historique` (`montant`, `trader`) VALUES('.$valeur.','.$trader['id'].')';
echo $sql;
echo "<br>";
$req = $bdd->query($sql);




	}
	catch (Exception $e) {
		echo 'Exception reçue : ',  $e->getMessage(), "\n";
	}
	
	

	
}



?>


	