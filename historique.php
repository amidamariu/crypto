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
		<td>Gain</td>
		<td>Gain 3h</td>
		<td>Gain 24h</td>
<td>Gain depuis début </td>
   </tr>';
$total_binance=0;

foreach ($data as $key => $value)
{

	echo "<tr>";
	echo "<td>".$value['date']."</td>";
	echo "<td>".number_format($value['montant'],0)."</td>";
	
	
	$total = $value['montant'];
	$total_absolu = $total + $tra->get_deja();
	
	$debut_mois = $tra->get_debut_mois();
	$debut = $tra->get_ini();
	
	echo "<td>".number_format($total-$debut_mois,2)."€ (".number_format((100*($total- $debut_mois))/$debut_mois,2)."%)</td>";
	
	
	if($key != count($data) -1 )
	{
		$diff = $value['montant'] - $data[$key+1]['montant'];
		if($diff > 0)
		{
		echo "<td BGCOLOR='green'>".number_format($diff,0)."€ (".(number_format(100*$diff/$data[$key+1]['montant'],2))."%)</td>";
		}
		else
		{
		echo "<td BGCOLOR='red' >".number_format($diff,0)."€ (".(number_format(100*$diff/$data[$key+1]['montant'],2))."%)</td>";
		}
	}
	else
	{
		echo "<td> </td>";
	}
	
	if($key < count($data) - 8 )
	{
		$diff = $value['montant'] - $data[$key+8]['montant'];
		if($diff > 0)
		{
			echo "<td BGCOLOR='green'>".number_format($diff,0)."€ (".(number_format(100*$diff/$data[$key+8]['montant'],2))."%)</td>";
		}
		else
		{
			echo "<td BGCOLOR='red' >".number_format($diff,0)."€ (".(number_format(100*$diff/$data[$key+8]['montant'],2))."%)</td>";
		}
	}
	else
	{
		echo "<td> </td>";
	}
	

	
	echo "<td>".number_format($total_absolu-$debut,2)."€ (".number_format((100*($total_absolu- $debut))/$debut,2)."%)</td>";
	
	echo "</tr>";

	
}

echo "</table>";
echo "</center>";
?>