<?php

include_once "class/connexion_class.php";
include_once "class/bitrex_class.php";
require_once 'class/kraken_class.php';
require_once 'class/binance_class.php';
class trader
{
  private $_id;
  private $_nom;
  private $_pfval;
  private $_graph;
  public function __construct($id) 
  {
 

  $bdd = Connexion::bdd();

  $this->_graph = array();
  $sql = 'SELECT * FROM trader WHERE id='.$id.' ';
$rep = $bdd->query($sql);
$donnee = $rep->fetch();
  $this->_id = $id;
  $this->_nom = $donnee["nom"];

  if ($donnee == null)
  {
	  echo "erreur";
  }
  

  $pf = $this->get_pf();

  
  
  $this->_pfval = array();
  
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
  	
  	
  	
  	
  	
  	

  	
  	if($valeurEUR > 10)
  	{
  		$this->_graph[$key]=$valeurEUR;
  		$this->_pfval[] = [
  				"plateforme" => $one['plateforme'],
  				"monnaie" => $key,
  				"quantite" => $one['quantite'],
  				"prixBTC" => $prixBTC,
  				"prixEUR" => $prixEUR,
  				"monnaie" => $key,
  				"valeur" => $valeurEUR,
  		];
  		
  	}
  	
  }
  
  $total = $this->get_total();
  
  foreach ($this->_graph as  $key => $value){
  	$this->_graph[$key] = 100*$value/$total;
  }
  

  
  
  }
  
  public function print_graph()
  {
  	
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
  						
  						foreach ($this->_graph as  $key => $value){
  							echo '{y: '.$value.', label: "'.$key.'"},';
  				}
  				
  				?>
		]
	}]
});
chart.render();

}
</script>

<?php

  }
  public function  table_pfval()
  {
  	
  	
  	echo "</table></div>";
  	
  	
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
  	
  	foreach ($this->_pfval as $one)
  	{
  		
  		
  		echo "<tr>";
  		echo "<td>".$one['plateforme']."</td>";
  		echo '<td> <a href="ordre.php?id='.$this->_id.'&plateforme='.$one['plateforme'].'&monnaie='.$one['monnaie'].'">'.$one['monnaie']."<a></td>";
  		echo "<td>".$one['quantite']."</td>";
  		echo "<td>".$one['prixBTC']."</td>";
  		echo "<td>".$one['prixEUR']."</td>";
  		echo "<td>".number_format($one['valeur'],2)."</td>";
  		echo "</tr>";
  		
  	}
  	
  	echo "</table></div>";
  	
  	
  }
  public function get_pfval()
  {
  
  	return $this->_pfval;
  }
  
  
    public function get_kraken()
  {
    $bdd = Connexion::bdd();
  $sql = 'SELECT * FROM trader WHERE id='.$this->_id.' ';
  	$rep = $bdd->query($sql);
$donnee = $rep->fetch();	
$beta = false; 
$url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
$sslverify = $beta ? false : true;
$version = 0;
$kraken = new KrakenAPI($donnee["kraken_key"],$donnee["kraken_secret"], $url, $version, $sslverify);
return $kraken;
  }
  
  
  public function get_binance()
  {
  	$bdd = Connexion::bdd();
  	$sql = 'SELECT * FROM trader WHERE id='.$this->_id.' ';
  	$rep = $bdd->query($sql);
  	$donnee = $rep->fetch();
  	$kraken = new Binance($donnee["binance_key"],$donnee["binance_secret"]);
  	return $kraken;
  }
  
  

  	public function stock_kraken()
  	{
  		try 
  		{
  		$kraken=$this->get_kraken();
  		$res=$kraken->QueryPrivate('Balance');
  		
  		$bdd = Connexion::bdd();
  		
  	//	$sql= "DELETE FROM `portefeuille` WHERE `trader_id`=".$this->_id." AND plateforme='kraken'";
  	//	$bdd->query($sql);
  		
  		$req_suppr = $bdd->prepare('DELETE FROM `portefeuille` WHERE `trader_id` = :trader  AND `monnaie` = :monnaie AND `plateforme` =
 :plateforme');
  		
  		$req_add = $bdd->prepare('INSERT INTO `portefeuille` (`trader_id`, `monnaie`, `plateforme`, `quantite`)
VALUES(:trader, :monnaie, :plateforme, :quantite)');
  		
  		foreach ($res['result'] as  $key => $value)
  		{
  			
  			$req_suppr->execute(array(
  					'trader' => $this->_id,
  					'monnaie' => $key,
  					'plateforme' => "kraken",
  			)) or die(print_r($req_suppr->errorInfo()));
  			
  				$req_add->execute(array(
  						'trader' => $this->_id,
  						'monnaie' => $key,
  						'plateforme' => "kraken",
  						'quantite' => $value
  				)) or die(print_r($req_add->errorInfo()));
  				
  		}
  		
  		
  		}
  		catch (Exception $e) {
  			echo 'Exception reçue : ',  $e->getMessage(), "\n";
  		}
  		
  	}
  	
  	
 
  	
  	public function stock_binance()
  	{
  		try
  		{
  			$bin=$this->get_binance();
  			$data = $bin->balances();
  	
  			
  			
  			$bdd = Connexion::bdd();
  			
  			$sql= "DELETE FROM `portefeuille` WHERE `trader_id`=".$this->_id." AND plateforme='binance'";
  			$bdd->query($sql);
  			
  			$req = $bdd->prepare('INSERT INTO `portefeuille` (`trader_id`, `monnaie`, `plateforme`, `quantite`)
VALUES(:trader, :monnaie, :plateforme, :quantite)');
  			
  			foreach ($data as $key => $value)
  			{
  				
  				if($value['available']!=0)
  				{
  				$req->execute(array(
  						'trader' => $this->_id,
  						'monnaie' => $key,
  						'plateforme' => "binance",
  						'quantite' => $value['available']
  				)) or die(print_r($req->errorInfo()));
  			
  			
  				}
  			}
  			
  			
  			
  			
  			
  		}
  		catch (Exception $e) {
  			echo 'Exception reçue : ',  $e->getMessage(), "\n";
  		}
  		
  	}
  	
  	
  	
  	
  	
  	
  	
  	public function stock_bitrex()
  	{
  		try
  		{
  			$bit=$this->get_bitrex();
  			
  			if($bit!= null )
  			{
  			$data = $bit->GetBalances();
  			
  			
  			
  			$bdd = Connexion::bdd();
  			
  			$sql= "DELETE FROM `portefeuille` WHERE `trader_id`=".$this->_id." AND plateforme='bitrex'";
  			$bdd->query($sql);
  			
  			$req = $bdd->prepare('INSERT INTO `portefeuille` (`trader_id`, `monnaie`, `plateforme`, `quantite`)
VALUES(:trader, :monnaie, :plateforme, :quantite)');

  			foreach ($data->result as  $one) {
  				
  		
  					$req->execute(array(
  							'trader' => $this->_id,
  							'monnaie' => $one->Currency,
  							'plateforme' => "bitrex",
  							'quantite' => $one->Balance
  					)) or die(print_r($req->errorInfo()));
  					
  					
  				
  			}
  			
  			
  			}
  			
  			
  		}
  		catch (Exception $e) {
  			echo 'Exception reçue : ',  $e->getMessage(), "\n";
  		}
  		
  	
  	}
  	

  	
  	public function stock_ordre_kraken()
  	{
  		$bdd = Connexion::bdd();
  		$kraken = $this->get_kraken();
  		
  		$res = $kraken->QueryPrivate('ClosedOrders',array('opt' => 10 ));
  		
  		$sql= "DELETE FROM `ordre` WHERE `trader_id`=".$this->$_id;
  		$bdd->query($sql);
  		$tab = $res['result']['closed'];
  		
  		$req = $bdd->prepare('REPLACE INTO `ordre` (`type`, `pair`, `monnaie1`, `monnaie2`,`quantite`, `prix`, `date`, `trader_id`, `plateforme`)
VALUES(:type, :pair, :monnaie1, :monnaie2,:quantite,:prix,:date,:trader,:plateforme)');
  		
  		foreach ($tab as  $line){
  			
  			$req->execute(array(
  					'type' => $line['descr']['type'],
  					'pair' => $line['descr']['pair'],
  					'monnaie1' => get_monnaie_by_pair($line['descr']['pair'],1),
  					'monnaie2' => get_monnaie_by_pair($line['descr']['pair'],2),
  					'quantite' => $line['vol_exec'],
  					'prix' => $line['price'],
  					'date' => date("Y-m-d H:i:s",$line['closetm']),
  					'trader' => $trader['id'],
  					'plateforme' => "kraken"
  			)) or die(print_r($req->errorInfo()));
  		}
  		
  	}
  	
  	
  	
  	
  	
  	
  	
  	
  	
  	
  	
  	
  	
  	
  	
  	
  	public function get_pf_kraken()
  	{
  		
  			$bdd = Connexion::bdd();
  			
  			$sql= "SELECT * FROM `portefeuille` WHERE `trader_id`=".$this->_id." AND plateforme='kraken'";
  			$donnee = $bdd->query($sql);
  			return $donnee->fetchAll();
  		
  	}
  	
  	public function get_pf()
  	{
  		
  		$bdd = Connexion::bdd();
  		
  		$sql= "SELECT * FROM `portefeuille` WHERE `trader_id`=".$this->_id." ORDER BY plateforme";
  		$donnee = $bdd->query($sql);
  		return $donnee->fetchAll();
  		
  	}
  	
  	
  	public function get_ordre_kraken()
  	{
  		
  		$bdd = Connexion::bdd();
  		
  		$sql= "SELECT * FROM `ordre` WHERE `trader_id`=".$this->_id." AND plateforme='kraken'";
  		$donnee = $bdd->query($sql);
  		return $donnee->fetchAll();
  		
  	}
  	
  	public function get_ordre($devise,$plateforme)
  	{
  		
  		$bdd = Connexion::bdd();
  		$devise = $bdd->quote($devise);
		$plateforme = $bdd->quote($plateforme);
  		$sql= "SELECT * FROM `ordre` WHERE `trader_id`=".$this->_id." AND plateforme=".$plateforme." AND monnaie1=".$devise;
  	
  		$donnee = $bdd->query($sql);
  		return $donnee->fetchAll();
  		
  	}
  	
  	
  	
  	
  	public function get_histo()
  	{
  		
  		$bdd = Connexion::bdd();
  		
  		$sql= "SELECT * FROM `historique` WHERE `trader`=".$this->_id." ORDER BY date DESC";
  	
  		$donnee = $bdd->query($sql);
  		return $donnee->fetchAll();
  		
  	}
  	
  	
  	public function get_minuit()
  	{
  		
  		$bdd = Connexion::bdd();
  		
  		$sql= "SELECT * FROM `historique` WHERE `trader`=".$this->_id." AND `date` > DATE(NOW()) ORDER BY `date` ASC LIMIT 1";
  		$donnee = $bdd->query($sql);
  		$donnee = $donnee->fetch();
  		return $donnee['montant'];
  	}
  	
  	public function get_lundi()
  	{
  		
  		$bdd = Connexion::bdd();
  		
  		$sql= "SELECT * FROM `historique` WHERE `trader`=".$this->_id." AND `date` > DATE_SUB(DATE(NOW()), INTERVAL DAYOFWEEK(NOW()) DAY) ORDER BY `date` ASC LIMIT 1";
  		$donnee = $bdd->query($sql);
  		$donnee = $donnee->fetch();
  		return $donnee['montant'];
  	}
  	
  	
  	
  	
  	
  	
  	public function get_pf_bitrex()
  	{
  		
  		$bdd = Connexion::bdd();
  		
  		$sql= "SELECT * FROM `portefeuille` WHERE `trader_id`=".$this->_id." AND plateforme='bitrex'";
  		$donnee = $bdd->query($sql);
  		return $donnee->fetchAll();
  		
  	}
  	
  	
  	public function get_pf_binance()
  	{
  		
  		$bdd = Connexion::bdd();
  		
  		$sql= "SELECT * FROM `portefeuille` WHERE `trader_id`=".$this->_id." AND plateforme='binance'";
  		$donnee = $bdd->query($sql);
  		return $donnee->fetchAll();
  		
  	}
  
  
  
      public function get_bitrex()
  {
    $bdd = Connexion::bdd();
  $sql = 'SELECT * FROM trader WHERE id='.$this->_id.' ';
  	$rep = $bdd->query($sql);
  	$donnee = $rep->fetch();
  	;
  	if($donnee["bitrex_key"] != NULL)
  	{
$kraken = new Bittrex($donnee["bitrex_key"],$donnee["bitrex_secret"]);
return $kraken;
  	}
  	else
  	{
  		return null;
  	}
  }
  
  
      public function get_ini()
  {
    $bdd = Connexion::bdd();
  $sql = 'SELECT * FROM trader WHERE id='.$this->_id.' ';
  	$rep = $bdd->query($sql);
$donnee = $rep->fetch();	
return $donnee["initial"];
  }
  
        public function get_deja()
  {
    $bdd = Connexion::bdd();
  $sql = 'SELECT * FROM trader WHERE id='.$this->_id.' ';
  	$rep = $bdd->query($sql);
$donnee = $rep->fetch();	
return $donnee["deja_recup"];
  }
  
          public function get_debut_mois()
  {
    $bdd = Connexion::bdd();
  $sql = 'SELECT * FROM trader WHERE id='.$this->_id.' ';
  	$rep = $bdd->query($sql);
$donnee = $rep->fetch();	
return $donnee["debut_mois"];
  }
  
  
  public function set_debut_mois()
  {
  	$bdd = Connexion::bdd();
  	$sql = "UPDATE `trader` SET `debut_mois` =".$this->get_total()." WHERE `id`=".$this->_id;
  	$rep = $bdd->query($sql);
  }
  
  
  public function get_total()
  {
  	$pf = $this->get_pf();
  	
  	$total = 0;
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
  		
  		$total = $total + $prixEUR*$one['quantite'];
  		
  		
  	}
  	
  	return $total;
  }
  
  
  
  
 
}
?>