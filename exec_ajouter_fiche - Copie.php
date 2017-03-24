<?php
	require_once("connexion.php");
	//10.76.8.44/developpements/suivi_etudes/fouad/exec_ajouter_fiche.php?service=ddtm76&pilotage_ddtm=srmt&annee_pgm=2017
	$etudes=new MesEtudes;
	$nomtable=$etudes->get_nomtablecomplet("etudes");
	$id_etude = $etudes->get_value("id_etude");
	$champs_creation = $etudes->get_value("champs_creation");
	$tab_champs_creation = explode(",",$champs_creation);
	$values ="[]";
	
	foreach($tab_champs_creation as $element) {
		if (isset($_GET[$element])) {
				$values .=",'".$_GET[$element]."'";
		};
	}
	$values=str_replace('[],','',$values);
	
	$requete  = 'insert into ' . $nomtable . ' ('.$champs_creation.') VALUES ('.$values.') returning '.$id_etude;
	$result = $etudes->exec_requete($requete) ;
	$row = $result->fetch(PDO::FETCH_ASSOC);
	echo $row[$id_etude];
	//echo $requete ;
?>

