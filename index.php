<!DOCTYPE HTML>
<html>
<head>


<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
  
  
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>

</head>
<body>

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
	
	$tra = new trader($id);
	
	echo "<H1> BTC : ".get_prix_sql("XXBTZEUR")."</H1>";
	echo "<H1> PF : ".number_format($tra->get_total(),2)."€ (".number_format($tra->get_total()/get_prix_sql("XXBTZEUR"),4)."B)</H1>";	echo date("Y-m-d H:i:s");
	
	echo "<br> <a href='historique.php?id=".$id."'> historique </a> ";

	
	



$pf = $tra->get_pf();


echo '
<div style="overflow-x:auto;">
<table id="example" class="display">
<thead>
   <tr>
       <td>Site </td>
       <td>Monnaie </td>
       <td>Quantité</td>
		<td>Prix BTC</td>
        <td>Prix EUR</td>
		<td>Valeur</td>
   </tr>
</thead>
';

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



$total = $total_kraken  + $total_bitrex+$total_binance;
$total_absolu = $total + $tra->get_deja();
$debut_mois = $tra->get_debut_mois();
$debut = $tra->get_ini();
echo "Binance :".number_format($total_binance,2)."<br>";
echo "Bitrex :".number_format($total_bitrex,2)."<br>";
echo "Kraken :".number_format($total_kraken,2)."<br>";
echo "TOTAL :".(number_format($total_kraken+$total_bitrex+$total_binance,2))."<br><br>";


echo "gain depuis : <br>";
echo "<br>minuit : ".print_evo($tra->get_minuit(),$total);
echo "<br>lundi : ".print_evo($tra->get_lundi(),$total);
echo "<br>dernier raz: ".print_evo($debut_mois,$total)." (<a href='raz.php?id=".$id."' >remettre à zero</a>)";
echo "<br>debut: ".print_evo($debut,$total_absolu);



foreach ($graph as  $key => $value){
	$graph[$key] = 100*$value/$total;
}


} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}

echo "</center>";

?>



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

<script>

$(document).ready(function() {
    $('#example').DataTable();
} );
</script>  


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