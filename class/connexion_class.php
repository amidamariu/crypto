<?php
 
class Connexion {
     
    public static function bdd() {
        // fichier contenant les informations pour se connecter
        $fichier = 'config/sql.ini';
        if(file_exists($fichier)) {
        $config = parse_ini_file($fichier, true);
     
        $moteur = $config['SQL']['moteur'];
        $hote   = $config['SQL']['hote'];
        $login  = $config['SQL']['login'];
        $mdp    = $config['SQL']['mdp'];
        $base   = $config['SQL']['base'];
     
        try {
        	$bdd = new PDO($moteur .':host='. $hote .';dbname='. $base, $login, $mdp, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_PERSISTENT => true ) );
        } catch (Exception $e) {
        die('Erreur : '. $e->getMessage());
        }
            return $bdd;
        }
	
    }
}

?>
