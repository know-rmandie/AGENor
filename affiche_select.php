<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
	$liste_select = $_GET['liste_select'];
	$nom_table = $_GET['nom_table'];
	$condition = $_GET['condition'];

	echo 'Liste des '.$nom_table . ': ';
	echo '<select name="select_'.$nom_table.'" size="1" id="select_'.$nom_table.'" onchange="javascript:displayVals();">'."\n" ;
	echo '<option value="" selected>Tous</option>'."\n"; 
	echo $etudes->affiche_select($liste_select,$nom_table,$condition)."\n";	
	echo "</select>\n";
	


?>

