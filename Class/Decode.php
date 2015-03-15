<?php
/**
 * Classe Decode
 *
 * Permet d'analyser le code fourni par l'utilisateur
 * @param String $data Text brut des instructions entrées par l'utilisateur
 * @author Baptiste Meunier baptiste.meunier0@gmail.com
 * @version 1.0
 **/
class Decode{

	public $var_used = array(); // Liste des variables utilisées
	public $decode = array();   // Liste des instructions formatées
	public $erreur = array();   // Liste des erreurs
	private $fonction_match  = array("~^CALCUL (.+)$~" => "calcul",
							   		 "~^LIRE (.+)$~" => "lire",
							   		 "~^AFFICHER (.+)$~" => "afficher",
							   		 "~^SET (.+)$~" => "set"
							   );   // Liste des fonction
	private $fonction_simple = array("STOP", "CLRTXT"); // Liste des instructions

	function __construct($data){
		$inctructions = explode("#", $data);      // On recupere les instuctions une par une (Qui sont sepater par un diese)
		foreach ($inctructions as $instruction) { // Pour chaque instruction
			foreach ($this->fonction_simple as $fonction) { // Si c'est une instruction simple
				if($instruction == $fonction){
					$this->instruction($fonction); // Ajouts de l'instructions
					break;
				}
			}
			foreach ($this->fonction_match as $match => $fonction) { // Si c'est une fonction complexe
				if(preg_match($match, $instruction, $find)){
					$this->$fonction($find[1]); // Ajouts de l'instructions
					break;
				}
			}
		}
	}
	/**
	 * Function varAdd
	 *
	 * Ajoute la variable au tableau des variables (si besoin)
	 * @param La variable à ajouter (String [A-Z])
	 * @return boolean variable ajoutée ou non
	 **/
	private function varAdd($var){
		$var = trim($var);
		if(strlen($var)==1 && !in_array($var, $this->var_used)){ // Si la variable n'est pas dejà dans le tableau
			$this->var_used[] = $var;
			return true;
		}
		return false;
	}
	/**
	 * Function instruction
	 *
	 * Ajoute l'instruction simple à la liste des insctructions
	 * @param String $fonction La fonction à ajouté et les parametres
	 * @return void
	 **/
	private function instruction($fonction){
			$this->decode[] = array('fonction' => 'instruction',
									'params' => ucfirst(strtolower($fonction)));
	}
	/**
	 * Function afficher
	 *
	 * Ajoute l'instruction afficher à la liste des insctructions
	 * @param String $text Le contenu à afficher 
	 * @return void
	 **/
	private function afficher($text){
			if(preg_match("/^[A-Z]$/", $text)){
				$params = array('var' => $text);
			}else{
				$params = array('text' => $text);				
			}
			$this->decode[] = array('fonction' => 'afficher',
									'params' => $params);
	}
	/**
	 * Function lire
	 *
	 * Ajoute l'instruction lire à la liste des insctructions
	 * @param Char/String $var La variable à lire 
	 * @return void
	 **/
	private function lire($var){
			$this->decode[] = array('fonction' => 'lire',
									'params' => array('var' => $var));
	}
	/**
	 * Function calcul
	 *
	 * Ajoute l'instruction calcul à la liste des insctructions
	 * @param String $params Le calcul à afficher 
	 * @return void
	 **/
	private function calcul($params){
			$params = explode("=", $params);
			$this->decode[] = array('fonction' => 'calcul',
									'params' => array('var' => $params[0], 'calcul' => $params[1]));
	}
	/**
	 * Function set
	 *
	 * Ajoute le parametre à la liste des insctructions
	 * @param String $text Le parametre à modifier
	 * @return void
	 **/
	private function set($set){
		$set = ucfirst(strtolower($set));
		$liste_set = array('Deg', 'Rad', 'Gra');
		if(in_array($set, $liste_set)){
			$this->decode[] = array('fonction' => 'set',
									'params' => array('set' => $set));
		}else{
			$this->erreur[] = 'WARNING#Parametre '.$set.' inconnu';
		}	
	}
}

?>