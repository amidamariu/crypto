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


$bdd = Connexion::bdd();
$sql = 'SELECT * FROM ordre WHERE 1';
$rep = $bdd->query($sql);

$tra = new trader(1);
$kraken = $tra->get_kraken();
$res = $kraken->QueryPublic('OHLC', array('pair' => 'XBTCZEUR', 'interval' => '1440','since' => '1013359168'));
echo "<pre>";
var_dump($res['result']);
echo "</pre>";
/*
foreach ( $rep->fetchAll() as $ordre)
{
	echo $ordre['date']."<br>";
}
*/

?>