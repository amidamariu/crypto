<?php

function get_prix($monnaie,$quantite,$id)
{

	if($quantite == 0)
	{
		
		return 0;
	}
	else
	{
$bdd = Connexion::bdd();

$sql = "SELECT * FROM `ordre` WHERE `trader_id`=".$id." AND `type`='buy' AND `monnaie1` = '".$monnaie."' ORDER BY date DESC";
$req = $bdd->query($sql);

$S_cout = 0;
$S_quantite = 0;
foreach($req->fetchAll() as $ordre)
{
if($S_quantite + $ordre['quantite'] < $quantite)
{
$S_cout = $S_cout + $ordre['quantite']*$ordre['prix'];
$S_quantite = $S_quantite + $ordre['quantite'];
}
else
{
$reste = $quantite-$S_quantite;
$S_cout = $S_cout + $reste*$ordre['prix'];
break;
}

}
return $S_cout/$quantite;
	}
}



function get_altname($monnaie)
{

$bdd = Connexion::bdd();
$sql = "SELECT * FROM `kraken_pair` WHERE `pair`=".'"'.$monnaie.'"';
$req = $bdd->query($sql);

$data = $req->fetch();

return $data['altname'];
}

function get_monnaie_by_pair($pair,$num)
{
	
	$bdd = Connexion::bdd();
	$sql = "SELECT * FROM `kraken_pair` WHERE `altname`=".'"'.$pair.'"';
	$req = $bdd->query($sql);
	$data = $req->fetch();
	if($num == 1)
	{
	return $data['monnaie1'];
	}
	else
	{
	return $data['monnaie2'];
	}
}




function get_prix_sql($monnaie)
{
	
	$bdd = Connexion::bdd();
	$sql = "SELECT * FROM `price` WHERE `monnaie`=".'"'.$monnaie.'"';
	$req = $bdd->query($sql);
	
	$data = $req->fetch();
	
	return $data['prix'];
}


function print_evo($montant1,$montant2)
{
	
	if($montant1 > $montant2)
	{
	return '<font color="red">'.number_format($montant2-$montant1,0)."€ (".number_format((100*($montant2- $montant1))/$montant1,0)." %) </font> ";
	}
	else
	{
	return '<font color="green">'.number_format($montant2-$montant1,0)."€ (".number_format((100*($montant2- $montant1))/$montant1,0)." %) </font> ";
	}
}




function get_prix_sql2($monnaie1,$monnaie2,$plateforme)
{
	if( ($monnaie1=='BTC' || $monnaie1=='XXBT') && $monnaie2 = "EUR" )
	{
		return	get_prix_sql("XXBTZEUR");
	}
	
	
	if($plateforme == "kraken")
	{
		if($monnaie2=="EUR")
		{
			$param = KrakenAPI::get_pair($monnaie1,'ZEUR');
		}
		if($monnaie2=="BTC")
		{
			$param = KrakenAPI::get_pair($monnaie1,'XXBT');
		}
		
	}
	
	if($plateforme == 'bitrex')
	{
		

			$param = "BTC-".$monnaie1;
		
	}
	
	if($plateforme == 'binance')
	{
			$param = $monnaie1.'BTC';
	}
	
	
	
	$bdd = Connexion::bdd();
	$sql = "SELECT * FROM `price` WHERE `monnaie`=".'"'.$param.'"';
	$req = $bdd->query($sql);
	
	$data = $req->fetch();
	
	$prix =  $data['prix'];
	
	if($plateforme != 'kraken' && $monnaie2 == 'EUR')
	{
		$prix = $prix * get_prix_sql("XXBTZEUR");
	}

	return $prix;
}



?>