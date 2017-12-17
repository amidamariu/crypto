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
$data = $tra->get_histo();
echo "<center>";

echo '
<div style="overflow-x:auto;">
<table BORDER>
   <tr>
       <td>Date </td>
       <td>Valeur </td>
		<td>Gain dernier retrait </td>
<td>Gain depuis début </td>
   </tr>';
$total_binance=0;

foreach ($data as $one)
{
	
	echo "<tr>";
	echo "<td>".$one['date']."</td>";
	echo "<td>".number_format($one['montant'],0)."</td>";

	$total = $one['montant'];
	$total_absolu = $total + $tra->get_deja();
	
	$debut_mois = $tra->get_debut_mois();
	$debut = $tra->get_ini();
	
	echo "<td>".number_format($total-$debut_mois,2)." (".number_format((100*($total- $debut_mois))/$debut_mois,2)."%)</td>";
	echo "<td>".number_format($total_absolu-$debut,2)." (".number_format((100*($total_absolu- $debut))/$debut,2)."%)</td>";
	
	echo "</tr>";
	
	
}

echo "</table>";
echo "</center>";
?>