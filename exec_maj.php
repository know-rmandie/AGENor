<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;

	function formater_sql($nom_champ) {
		$type=explode('_',$nom_champ);
		$valeur = $_POST[$nom_champ] ;
		$valeur= str_replace("'","''", $valeur);
		if ($type[0]=='date') {
			$valeur= str_replace("/","-", $valeur);
			if(($valeur=='--') OR ($valeur=='')){return $nom_champ .' = NULL';};
			$fdate = explode('-',$valeur);
			return $nom_champ ." = '". $fdate[0].'-'.$fdate[1].'-'.$fdate[2]."'";
		};

		if ($type[0]=='montant') {
			$valeur= str_replace(",",".", $valeur);
			$valeur= str_replace(" ","", $valeur);
			if ($valeur=='') {return $nom_champ ." = 0.00";};
			//return $nom_champ ." = '". number_format($valeur, 2, '.', '')."'";
			return $nom_champ ." = ". number_format($valeur, 2, '.', '');
		}; 
		
		if ($type[0]=='pourcentage') {
			$valeur= str_replace(",",".", $valeur);
			$valeur= str_replace(" ","", $valeur);
			$valeur = round($valeur,0);
			return $nom_champ ." = ".$valeur;
		};
		
		if ($type[0]=='abandon') {
			//return $nom_champ .' = true';
			if ($valeur=='true') {return $nom_champ .' = true';};
			if ($valeur=='false') {return $nom_champ .' = false';};
		}; 

		if ($type[0]=='priorite') { 
			if ($valeur=='') {$valeur =0;};
			return $nom_champ ." = ".$valeur;
		}; 
		
		if (($type[0]=='service') or ($type[0]=='pilotage_ddtm')) {
			$valeur= str_replace('\"','"', $valeur);
			}
				
		//$valeur= str_replace("\\r\\n","\r\n", $valeur);

			
		return $nom_champ ." = '". $valeur."'";
	}

	$champ = explode(',',$etudes->_liste_champs_etude);
	$sql ='';
	foreach($champ as $element) {
		if (isset($_POST[$element])) {
				if ($sql!=''){$sql=$sql.', ';}
				$sql= $sql. formater_sql($element) ;
		};
	};
	$nom_champ_id_etude=$etudes->get_nom_champ_id_etude();
	$nomtable=$etudes->get_nomtablecomplet("etudes");
	$requete  = 'update ' . $nomtable . ' SET '. $sql .' WHERE '.$nom_champ_id_etude.' = '.$_POST[id_etude] ;
	$etudes->exec_requete($requete) ;
?>

