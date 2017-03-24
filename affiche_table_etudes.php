<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
	$annee = $_GET['annee'];
	$dreal = $_GET['dreal'];
	$bop = $_GET['bop'];
	$service = $_GET['service'];
	$pilote = $_GET['pilote'];
	$titre = $_GET['titre'];
	$avanc = $_GET['avanc'];
	$themes = $_GET['themes'];
	echo $etudes->table_etudes($annee,$dreal,$bop,$service,$pilote,$titre,$avanc,$themes);
?>
