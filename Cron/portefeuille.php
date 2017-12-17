<?php
chdir("..");
require_once 'class/binance_class.php';
require_once 'class/trader_class.php';



$bdd = Connexion::bdd();

$sql = "SELECT * FROM `trader` WHERE 1";
$req = $bdd->query($sql);


foreach($req->fetchAll() as $trader)
{
	$tra = new trader($trader['id']);
	
	try {
		
		
$tra->stock_kraken();
	}
	catch (Exception $e) {
		echo 'Exception reçue : ',  $e->getMessage(), "\n";
	}
	
	
	try {
		
		
		$tra->stock_binance();
	}
	catch (Exception $e) {
		echo 'Exception reçue : ',  $e->getMessage(), "\n";
	}
	
	try {
		
		
		$tra->stock_bitrex();
	}
	catch (Exception $e) {
		echo 'Exception reçue : ',  $e->getMessage(), "\n";
	}
	
	
}


	
/*
$tra = new trader(2);
$bin = $tra->get_binance();
$data = $bin->balances();

foreach ($data as $key => $value)
{
	
	
	$bdd = Connexion::bdd();
	
if($value['available']!=0)
{
	
	$sql = "REPLACE INTO portefeuille (trader_id,monnaie,plateforme,quantite) VALUES (1,'EUR','kraken',23)";
	$req = $bdd->query($sql);
	
}






}

*/


?>