<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
	$liste_select = $_GET['liste_select'];
	$larg_table = $_GET['larg_table'];
	$nom_table = $_GET['nom_table'];
	$condition = $_GET['condition'];
	echo '<table id="'.$nom_table.'"  class="display">'.$etudes->affiche_table($liste_select,$larg_table,$nom_table,$condition).'</table>';
?>

