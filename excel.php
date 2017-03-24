<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
	$annee = $_POST['Excel_annee'];
	$dreal = $_POST['Excel_dreal'];
	$service = $_POST['Excel_service'];
	$bop = $_POST['Excel_bop'];
	$pilote = $_POST['Excel_pilotage_ddtm'];
	$titre = $_POST['Excel_titre'];
	$avanc = $_POST['Excel_avanc'];
	$thematique = $_POST['Excel_thematiques'];
	
	if(!empty($_POST['mes_cases']))
		{ 
		$malistedechamp='[]';
		foreach($_POST['mes_cases'] as $val)
			{
			$malistedechamp .= ','. $val;
			} 
		$malistedechamp=str_replace('[],','',$malistedechamp);
		} else {
		$malistedechamp = $etudes->_liste_champs_etude ;
		}
		
	$sql="select $malistedechamp from ".$etudes->get_nomtablecomplet('etudes')." where ".$etudes->get_value('id_etude')." in (
		select distinct ".$etudes->get_value('id_etude')."
			from ".$etudes->get_value('schema').".tableau_format('$annee', '$dreal', '$bop', '$service', '$pilote', '$titre' , '$avanc','$thematique')  
			as
			f00(
			 index text,
			 id_etude_hn int,
			 rang varchar(1),
			 pointeur integer,
			 titre varchar(200),
			 service varchar(30),
				 pilotage_ddtm varchar(30),
				 annee_pgm varchar(10),
				 montant_demande_lb varchar(20),
				 montant_autorisation_lb varchar(20),
				 montant_engagement_lb varchar(20),
				 date_maj varchar(10),
				 avanc_etude varchar(10)
				 ) 
			WHERE rang<>'A')
		ORDER BY dreal, bop, ligne_budgetaire_lb, libelle 
		";
	

	$result = $etudes->exec_requete($sql);
	$table ='<table>';
	$table .='<thead>';
	$row = $result->fetch(PDO::FETCH_ASSOC);
	// array_keys prend les entêtes des colonnes
	$table .= '<tr><th>' . implode('</th><th>',array_keys($row)). '</th></tr>';
	$table .=  '</thead>';
	// lecture de la table et mise en place des class et id pour les css
	do {
		$temp =  '<tr><td>'. implode('</td><td>', $row). '</td></tr>' ;
		$table .=  $temp."\n" ;
	} while($row = $result->fetch(PDO::FETCH_ASSOC));
	$table .="</table>";
	header("Content-disposition: attachment; filename=agenor.xls");
	header('Content-Type: text/html; charset=iso-8859-1');
	header("Content-Type: application/force-download");
	header("Content-Transfer-Encoding: application/vnd.ms-excel\n");
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");	
	echo utf8_decode($table) ;
?>