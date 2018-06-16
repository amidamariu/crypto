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


$r = 42;

if ($r > 1 || $id !=3)
{
echo "<center>";
try {

	$grap = array();
	
	$tra = new trader($id);
	
	echo "<H1> BTC : ".get_prix_sql("XXBTZUSD")."</H1>";
	echo "<H1> PF : ".number_format($tra->get_total(),2)."$ (".number_format($tra->get_total()/get_prix_sql("XXBTZUSD"),4)."B)</H1>";	echo date("Y-m-d H:i:s");
	
	echo "<br> <a href='historique.php?id=".$id."'> historique </a> ";
	echo "<br> <a href='graph.php?id=".$id."'> graph </a> ";
	
$total = $tra->get_total();
$total_absolu = $total + $tra->get_deja();
$debut_mois = $tra->get_debut_mois();
$debut = $tra->get_ini();

echo "<center>";
echo "<br> gain depuis : <br>";
echo "<br>minuit : ".print_evo($tra->get_minuit(),$total);
echo "<br>lundi : ".print_evo($tra->get_lundi(),$total);
echo "<br>1er : ".print_evo($tra->get_premier(),$total);
echo "<br>dernier raz: ".print_evo($debut_mois,$total)." (<a href='raz.php?id=".$id."' >remettre à zero</a>)";
echo "<br>debut: ".print_evo($debut,$total_absolu);
echo "</center>";


} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}

echo "</center>";

$tra->print_graph();

$data = $tra->get_histo24();

?>





<script>

$(document).ready(function() {
    $('#example').DataTable( {
        "order": [[ 6, "desc" ]],
        "paging":   false
    } );
} );
</script>  


<div id="graph" style="height: 370px; width: 100%;"></div>


<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<div id="chartContainer24" style="height: 370px; width: 100%;"></div>
<div id="chartContainer24BTC" style="height: 370px; width: 100%;"></div>


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


<script>
function hist24() 
{




	
var chart = new CanvasJS.Chart("chartContainer24", {
	theme: "light2", // "light1", "light2", "dark1", "dark2"
	animationEnabled: true,
	zoomEnabled: true,
	title: {
		text: "Evolution FIAT"
	},
	 axisY:{
	        includeZero: false
	      },
	data: [{
		type: "area",
		dataPoints: []
	}]
});

var chart2 = new CanvasJS.Chart("chartContainer24BTC", {
	theme: "light2", // "light1", "light2", "dark1", "dark2"
	animationEnabled: true,
	zoomEnabled: true,
	title: {
		text: "Evolution BTC"
	},
	 axisY:{
	        includeZero: false
	      },
	data: [{
		type: "area",
		dataPoints: []
	}]
});






<?php 
$i=0;
foreach ($data as $key => $value)
{
	if($i!=0)
	{
		if(1- abs($precedent - $value['montant'])/$value['montant'] > 0.90)
		{
			
			echo 'dateString ="'.$value['date'].'";';
			echo "d = new Date(dateString.replace(' ', 'T'));";
			echo 'chart.options.data[0].dataPoints.push({x: d,y: '.$value['montant'].'});';	
			
			if($value['montantBTC']!=0)
			{
			echo 'chart2.options.data[0].dataPoints.push({x: d,y: '.$value['montantBTC'].'});';
			}
		}
		
	}
	else
	{
		echo 'dateString ="'.$value['date'].'";';
		echo "d = new Date(dateString.replace(' ', 'T'));";
		echo 'chart.options.data[0].dataPoints.push({x: d,y: '.$value['montant'].'});';	
		if($value['montantBTC']!=0)
		{
		echo 'chart2.options.data[0].dataPoints.push({x: d,y: '.$value['montantBTC'].'});';
		}
		}
	
	

	
	
	
	
	$i = $i -1;
	$precedent = $value['montant'];
}


?>
 chart.render();
 chart2.render();

function addDataPoints(noOfDps) {
	var xVal = chart.options.data[1].dataPoints.length + 1, yVal = 100;
	for(var i = 0; i < noOfDps; i++) {
		yVal = yVal +  Math.round(5 );
		chart.options.data[1].dataPoints.push({x: xVal,y: yVal});	
		xVal++;
	}
}




}

window.addEventListener("load",hist24,false);

</script>





