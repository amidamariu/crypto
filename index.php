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


$r = rand(0,10);

if ($r > 1 || $id !=3)
{
echo "<center>";
try {

	$grap = array();
	
	echo "<H1>".get_prix_sql("XXBTZEUR")."</H1>";
	echo date("Y-m-d H:i:s");
	
	


$tra = new trader($id);



$pf = $tra->get_pf();


echo '
<div style="overflow-x:auto;">
<table BORDER>
   <tr>
       <td>Site </td>
       <td>Monnaie </td>
       <td>Quantité</td>
		<td>Prix BTC</td>
        <td>Prix EUR</td>
		<td>Valeur</td>
   </tr>';

$total_binance=0;
$total_bitrex=0;
$total_kraken=0;

foreach ($pf as $one)
{
	
	$key = $one['monnaie'];
	
	


	if($key=='BTC' || $key=='XXBT')
	{
		$prixBTC = 1;
	}
	else
	{
		$prixBTC = get_prix_sql2($key,'BTC',$one['plateforme']);
	
	}
	
	if($key=='ZEUR')
	{
		$prixEUR = 1;
	}
	else
	{
		$prixEUR = get_prix_sql2($key,'EUR',$one['plateforme']);
	}
	
	$valeurEUR = $prixEUR*$one['quantite'];
	



	
	
	if($one['plateforme']=="binance")
	{
		$total_binance = $total_binance + $valeurEUR;
	}
	if($one['plateforme']=="kraken")
	{
		$total_kraken= $total_kraken+ $valeurEUR;
	}
	if($one['plateforme']=="bitrex")
	{
		$total_bitrex= $total_bitrex + $valeurEUR;
	}
	
	if($valeurEUR > 10)
	{
	$graph[$key]=$valeurEUR;
	echo "<tr>";
	echo "<td>".$one['plateforme']."</td>";
	echo '<td> <a href="ordre.php?id='.$id.'&plateforme='.$one['plateforme'].'&monnaie='.$key.'">'.$key."<a></td>";
	echo "<td>".$one['quantite']."</td>";
	echo "<td>".$prixBTC."</td>";
	echo "<td>".$prixEUR."</td>";	
	echo "<td>".number_format($valeurEUR,2)."</td>";
	echo "</tr>";
	}
}

echo "</table></div>";



/*
$pf_binance = $tra->get_pf_binance();


echo '
<div style="overflow-x:auto;">
<table BORDER>
   <tr>
       <td>Site </td>
       <td>Monnaie </td>
       <td>Quantité</td>
		<td>Prix BTC</td>
        <td>Prix EUR</td>
		<td>Valeur</td>
   </tr>';
$total_binance=0;

foreach ($pf_binance as $one)
{

	$key = $one['monnaie'];
	
	
		echo "<tr>";
		echo "<td> Binance </td>";
		echo "<td>".$key."</td>";
		echo "<td>".$one['quantite']."</td>";
		if($key=='BTC')
		{
			$prixBTC = 1;
		}
		else
		{
			$prixBTC = get_prix_sql2($key,'BTC','binance');
		}
		echo "<td>".$prixBTC."</td>";
		echo "<td>".get_prix_sql2($key,'EUR','binance')."</td>";
		$valeur = $prixBTC* get_prix_sql("XXBTZEUR")*$one['quantite'];
		$total_binance=$total_binance+$valeur;
		$graph[$key]=$valeur;
		echo "<td>".number_format($valeur,2)."</td>";
		echo "</tr>";
	
}

$total_bitrex=0;

$pf_bitrex = $tra->get_pf_bitrex();

foreach ($pf_bitrex as  $one){
	
	
	
	echo "<tr>";
	echo "<td> Bitrex</td>";
	echo "<td>".$one['monnaie']."</td>";
	echo "<td>".$one['quantite']."</td>";
	$prixBTC =get_prix_sql2($one['monnaie'],'BTC','bitrex');
	echo "<td>".$prixBTC."</td>";
	echo "<td>".get_prix_sql2($one['monnaie'],'EUR','bitrex')."</td>";
	$valeur = $prixBTC* get_prix_sql("XXBTZEUR")*$one['quantite'];
	$total_bitrex=$total_bitrex+$valeur;
	$graph[$one['monnaie']]=$valeur;
	echo "<td>".$valeur."</td>";
	echo "</tr>";
}



$total_kraken= 0;

$donnee = $tra->get_pf_kraken();

foreach ($donnee as $one){
	
	$key = $one['monnaie'];
	$value = $one['quantite'];
echo "<tr>";
echo "<td> kraken </td>";
echo "<td>".$key."</td>";

echo "<td>".number_format($value,3)."</td>";

if($key != 'ZEUR')
{
	$prix = get_prix_sql2($key,'EUR','kraken');
}
else
{ 
$prix = 1;
}

if($key != 'XXBT')
{
	$prix_btc = get_prix_sql2($key,'BTC','kraken');
}
else
{
	$prix_btc = 1;
}

echo "<td>".number_format($prix_btc,5)."</td>";
echo "<td>".number_format($prix,3)."</td>";
echo "<td>".number_format($prix*$value,2)."</td>";
$graph[$key]=$prix*$value;
echo "</tr>";
$total_kraken= $total_kraken+ $prix*$value;
}

echo "</table></div>";


number_format($prix,3);
*/
$total = $total_kraken  + $total_bitrex+$total_binance;
$total_absolu = $total + $tra->get_deja();
$debut_mois = $tra->get_debut_mois();
$debut = $tra->get_ini();
echo "Binance :".number_format($total_binance,2)."<br>";
echo "Bitrex :".number_format($total_bitrex,2)."<br>";
echo "Kraken :".number_format($total_kraken,2)."<br>";
echo "TOTAL :".(number_format($total_kraken+$total_bitrex+$total_binance,2))."<br><br>";



echo "<br> benef dernier retrait: ".number_format($total-$debut_mois,2);
echo "<br> Pourcentage dernier retrait: ".number_format((100*($total- $debut_mois))/$debut_mois,2);
echo "<br> benef debut: ".number_format($total_absolu-$debut,2);
echo "<br> Pourcentage debut: ".number_format((100*($total_absolu- $debut))/$debut,2);



foreach ($graph as  $key => $value){
	$graph[$key] = 100*$value/$total;
}


} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}

echo "</center>";

?>



<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function() {

var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	title: {
		text: "Repartition"
	},
	data: [{
		type: "pie",
		startAngle: 240,
		yValueFormatString: "##0.00\"%\"",
		indexLabel: "{label} {y}",
		dataPoints: [

			<?php  
			
			foreach ($graph as  $key => $value){
				echo '{y: '.$value.', label: "'.$key.'"},';
			}
			
			?>
		]
	}]
});
chart.render();

}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>


<?php 


}
else
{
	
	echo "soit mignon avec puzzle <br> La proba est à 10%";
}

?>