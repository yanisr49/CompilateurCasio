<?php
/**
 * Classe Decode
 *
 * Permet d'analyser le code fourni par l'utilisateur
 * @param Text brut des instructions entrées par l'utilisateur
 **/
class Decode{

	public $var_used = array(); // Liste des variables utilisées
	public $decode = array();   // Liste des instructions formatées
	public $erreur = array();   // Liste des erreurs
	private $fonction_match  = array("~^CALCUL (.+)$~" => "calcul",
							   		 "~^LIRE (.+)$~" => "lire",
							   		 "~^AFFICHER (.+)$~" => "afficher"
							   );   // Liste des fonction
	private $fonction_simple = array("STOP", "CLRTXT");

	function __construct($data){
		$inctructions = explode("#", $data);      // On recupere les instuctions une par une (Qui sont sepater par un diese)
		foreach ($inctructions as $instruction) { // Pour chaque instruction
			$find = false;
			foreach ($this->fonction_simple as $fonction) {
				if($instruction == $fonction){
					$this->instruction($fonction); // Ajouts de l'instructions
					$find = true;
					break;
				}
			}
			if($find == false){
				foreach ($this->fonction_match as $match => $fonction) {
					if(preg_match($match, $instruction, $find)){
						$this->$fonction($find[1]); // Ajouts de l'instructions
						$find = true;
						break;
					}
				}
			}
			if($find == false){
				$this->erreur[] = 'ERROR#Fonction '.$instruction.' inconnu';
				break;
			}
		}
	}
	/**
	 * Function varAdd
	 *
	 * Ajoute la variable au tableau des variables (si besoin)
	 * @param La variable à ajouter (String [A-Z])
	 * @return Boolean variable ajoutée ou non
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
	 * Ajoute l'instruction à la liste des insctructions
	 * @param La fonction à ajouté et les parametres
	 **/
	private function instruction($params){
			$this->decode[] = array('fonction' => 'instruction',
									'params' => ucfirst(strtolower($params)));
	}

	private function afficher($text){
			$this->decode[] = array('fonction' => 'afficher',
									'params' => array('text' => $text));
	}
	
	private function lire($var){
			$this->decode[] = array('fonction' => 'lire',
									'params' => array('var' => $var));
	}
	
	private function calcul($params){
			$params = explode("=", $params);
			$this->decode[] = array('fonction' => 'calcul',
									'params' => array('var' => $params[0], 'calcul' => $params[1]));
	}
}

?>