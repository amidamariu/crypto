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


$r = rand(0,100);

if ($r > 1 || $id !=3)
{
echo "<center>";
try {

	$grap = array();
	
	$tra = new trader($id);
	
	echo "<H1> BTC : ".get_prix_sql("XXBTZEUR")."</H1>";
	echo "<H1> PF : ".number_format($tra->get_total(),2)."€ (".number_format($tra->get_total()/get_prix_sql("XXBTZEUR"),4)."B)</H1>";	echo date("Y-m-d H:i:s");
	
	echo "<br> <a href='historique.php?id=".$id."'> historique </a> ";


$total = $tra->get_total();
$total_absolu = $total + $tra->get_deja();
$debut_mois = $tra->get_debut_mois();
$debut = $tra->get_ini();

echo "<center>";
echo "<br> gain depuis : <br>";
echo "<br>minuit : ".print_evo($tra->get_minuit(),$total);
echo "<br>lundi : ".print_evo($tra->get_lundi(),$total);
echo "<br>dernier raz: ".print_evo($debut_mois,$total)." (<a href='raz.php?id=".$id."' >remettre à zero</a>)";
echo "<br>debut: ".print_evo($debut,$total_absolu);
echo "</center>";


} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}

echo "</center>";

$tra->print_graph();

?>





<script>

$(document).ready(function() {
    $('#example').DataTable( {
        "order": [[ 5, "desc" ]]
    } );
} );
</script>  


<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>


<?php 



$tra->table_pfval();
/*
echo "Binance :".number_format($total_binance,2)."<br>";
echo "Bitrex :".number_format($total_bitrex,2)."<br>";
echo "Kraken :".number_format($total_kraken,2)."<br>";
echo "TOTAL :".(number_format($total_kraken+$total_bitrex+$total_binance,2))."<br><br>";
*/

}
else
{
	
	echo "soit mignon avec puzzle <br> La proba est à 10%";
}

?>