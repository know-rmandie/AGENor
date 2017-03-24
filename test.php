<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
	echo "test</br>";
	echo $etudes->get_bop();
	echo "test2</br>";
?>
<!DOCTYPE html>