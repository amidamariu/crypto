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


$tra = new trader(1);
$kra = $tra->get_kraken();
echo "<pre>";
var_dump($kra->QueryPublic('Assets'));
echo "</pre>";
?>