<?php

include_once "class/connexion_class.php";
include_once "class/bitrex_class.php";
require_once 'class/kraken_class.php';
require_once 'class/binance_class.php';
class trader
{
  private $_id;
  private $_nom;
  public function __construct($id) 
  {
 

  $bdd = Connexion::bdd();

	
  $sql = 'SELECT * FROM trader WHERE id='.$id.' ';
$rep = $bdd->query($sql);
$donnee = $rep->fetch();
  $this->_id = $id;
  $this->_nom = $donnee["nom"];

  if ($donnee == null)
  {
	  echo "erreur";
  }

  
  
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
  		
  		$sql= "SELECT * FROM `ordre` WHERE `trader_id`=".$this->_id." AND plateforme='".$plateforme."' AND monnaie1='".$devise."'";
  		echo $sql;
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