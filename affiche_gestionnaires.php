<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
	$nom_table ="budgets";
	$condition = "WHERE 1=1 ORDER BY gestionnaire";
	$liste_select = "DISTINCT gestionnaire, gestionnaire as gest";
    $mon_id=($_GET["mon_id"]=='')?"select_gestionnaire":"dreal";
	echo 'Gestionnaires : ';
	echo '<select name="'.$mon_id.'" size="1" id="'.$mon_id.'" onchange="javascript:displayVals();">'."\n" ;
	echo '<option value="" selected>Tous</option>'."\n"; 
	echo $etudes->affiche_select($liste_select,$nom_table,$condition)."\n";	
	echo "</select>\n";

?>

