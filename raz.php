<?php
/**
 * Example usage of the KrakenAPIClient library.
 *
 * See https://www.kraken.com/help/api for more info.
 *
 */
require_once 'class/binance_class.php';
require_once 'class/connexion_class.php';
require_once 'class/trader_class.php';
require_once 'class/bitrex_class.php';
require_once 'fonction/vrac.php'; 


if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
else
{
	$id= 1;
}


$tra = new trader($id);
$tra->set_debut_mois();

?>