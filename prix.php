<?php
/**
 * Example usage of the KrakenAPIClient library. 
 *
 * See https://www.kraken.com/help/api for more info.
 *
 */
require_once 'class/connexion_class.php'; 
require_once 'class/trader_class.php'; 

$tra = new trader(2);
$tra->stock_bitrex();




?>