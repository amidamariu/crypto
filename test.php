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


$bdd = Connexion::bdd();
$sql = "SELECT * FROM `price` WHERE 1";
$req = $bdd->query($sql);

$data = $req->fetchAll();




$key = array_search('BCXBTC', array_column($data, 'monnaie'));
if(is_null($key))
{
echo "null";
}
echo $data[$key]['prix'];

var_dump($data);

?>