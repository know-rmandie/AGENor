<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
	$id=$_GET["id_etude"];
	// echo $etudes->lit_table_etude('','','','','','','id_etude='.$id) ;
	echo $etudes->get_etude($id) ;
?>