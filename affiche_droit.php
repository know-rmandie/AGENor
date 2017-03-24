<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
	$mdp=$_GET["mdp"];
	$login=$_GET["login"];
	echo $etudes->get_droit($login,$mdp) ;
?>