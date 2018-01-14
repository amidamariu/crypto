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
$data = $tra->get_histo24();





?>


<!DOCTYPE HTML>
<html>
<head>
<script>
window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer24", {
	theme: "light2", // "light1", "light2", "dark1", "dark2"
	animationEnabled: true,
	zoomEnabled: true,
	title: {
		text: "Evolution"
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
	$i = $i -1;
	echo 'chart.options.data[0].dataPoints.push({x: new Date("'.$value['date'].'"),y: '.$value['montant'].'});';	
}


?>
chart.render();

function addDataPoints(noOfDps) {
	var xVal = chart.options.data[0].dataPoints.length + 1, yVal = 100;
	for(var i = 0; i < noOfDps; i++) {
		yVal = yVal +  Math.round(5 );
		chart.options.data[0].dataPoints.push({x: xVal,y: yVal});	
		xVal++;
	}
}

}
</script>
</head>
<body>
<div id="chartContainer24" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>
