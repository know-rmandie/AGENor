<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
	
	function formater($type,$valeur) {
		$valeur= str_replace("'","''", $valeur);
		
		if (strpos($type,'date')!== false) {
			$valeur= str_replace("/","-", $valeur);
			if(($valeur=='--') OR ($valeur=='')){return 'NULL';};
			$fdate = explode('-',$valeur);
			return "'".$fdate[0].'-'.$fdate[1].'-'.$fdate[2]."'";
		};

		if (strpos($type,'montant')!== false) {
			$valeur= str_replace(",",".", $valeur);
			$valeur= str_replace(" ","", $valeur);
			if ($valeur=='') {return "0.00";};
			return number_format($valeur, 2, '.', '');
		}; 
		
		if (strpos($type,'pourcentage')!== false) { 
			$valeur= str_replace(",",".", $valeur);
			$valeur= str_replace(" ","", $valeur);
			$valeur = round($valeur,0);
			return $valeur;
		};
		
		if (strpos($type,'abandon')!== false) { 
			if ($valeur=='oui') {return 'true';};
			return 'false';
		}; 

		if (strpos($type,'priorite')!== false) { 
			if ($valeur=='') {$valeur =0;};
			return $valeur;
		}; 
		
		if ((strpos($type,'service')!== false) or (strpos($type,'pilotage_ddtm')!== false)) {
			$valeur= str_replace('\"','"', $valeur);
			}
					
		return "'". $valeur."'";
	}
	
	$nomtable=$etudes->get_nomtablecomplet("etudes");
	
	$id_etude = $etudes->get_value("id_etude"); 
	$champ = explode(',',$etudes->_liste_champs_etude);
	$values ="[]";
	$champs_retenus ="[]";
	
	foreach($champ as $element) {
		if (isset($_GET[$element])) {
				$values .=",".formater($element,$_GET[$element]);
				$champs_retenus.=",".$element;
		};
	}
	$values=str_replace('[],','',$values);
	$champs_retenus=str_replace('[],','',$champs_retenus);
	
	$requete  = 'insert into ' . $nomtable . ' ('.$champs_retenus.') VALUES ('.$values.') returning '.$id_etude;
	$result = $etudes->exec_requete($requete) ;
	$row = $result->fetch(PDO::FETCH_ASSOC);
	echo $row[$id_etude];
	//echo $requete;
?>

