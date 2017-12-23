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

$devise = $_GET['monnaie'];
$plateforme = $_GET['plateforme'];

$tra = new trader($id);
$data = $tra->get_ordre($devise,$plateforme);

echo '
<div style="overflow-x:auto;">
<table BORDER>
   <tr>
       <td>Site </td>
       <td>type </td>
       <td>monnaie1</td>
		<td>monnaie2</td>
        <td>quantite</td>
		<td>prix achat</td>
		<td>prix actu</td>
		<td>diff</td>
		
<td>Date</td>
   </tr>';
$total_binance=0;

foreach ($data as $one)
{
	echo "<tr>";
	echo "<td>".$one['plateforme']."</td>";
	echo "<td>".$one['type']."</td>";
	
	echo "<td>".$one['monnaie1']."</td>";
	echo "<td>".$one['monnaie2']."</td>";
	echo "<td>".$one['quantite']."</td>";
	if($one['monnaie2'] == 'ZEUR')
	{
		$prix_achat = $one['prix'];
		echo "<td><b>".$prix_achat."</b></td>";
		$prix_actu = get_prix_sql(KrakenAPI::get_altname($one['pair']));
		echo "<td><b>".$prix_actu."</b></td>";
		if($prix_actu-$prix_achat > 0)
		{
			echo "<td 	bgcolor='green'>".($prix_actu-$prix_achat)."</td>";
		}
		else
		{
			echo "<td 	bgcolor='red'>".($prix_actu-$prix_achat)."</td>";
		}
	}
	if($one['monnaie2'] == 'XXBT')
	{
		$prix_achat = $one['prix'];
		echo "<td><b>".$prix_achat."</b></td>";
		$prix_actu = get_prix_sql(KrakenAPI::get_altname($one['pair']));
		echo "<td><b>".$prix_actu."</b></td>";
		echo "<td>".($prix_actu-$prix_achat)."</td>";
		
	}
	echo "<td>".$one['date']."</td>";
	echo "</tr>";
}

echo "</table>";

?>