<?php require_once("param_server.php"); ?>
<?php
// mise à jour septembre 2013

// -------------- init variables ------------------
// Attention, sauvegarder le document en utf-8
// pour assurer la compatibilité avec les serveurs
// pour le passsage de caractères accentués

function formater_sql($valeur) {
	$f_sql=$valeur;
	$type=explode('_',$valeur);
	$valeur = $_POST[$valeur] ;
	$valeur= str_replace("\'","''", $valeur);
	if ($type[0]=='date') {
		$valeur= str_replace("/","-", $valeur);
		if(($valeur=='--') OR ($valeur=='')){return $f_sql .' = NULL';};
 		$fdate = explode('-',$valeur);
 		return $f_sql ." = '". $fdate[0].'-'.$fdate[1].'-'.$fdate[2]."'";
	};

	if ($type[0]=='montant') {
		$valeur= str_replace(",",".", $valeur);
		$valeur= str_replace(" ","", $valeur);
		if ($valeur=='') {return $f_sql ." = 0.00";};
		//return $f_sql ." = '". number_format($valeur, 2, '.', '')."'";
		return $f_sql ." = ". number_format($valeur, 2, '.', '');
	}; 
	
	if ($type[0]=='pourcentage') {
		$valeur= str_replace(",",".", $valeur);
		$valeur= str_replace(" ","", $valeur);
		$valeur = round($valeur,0);
		return $f_sql ." = ".$valeur;
	};
	
	if ($type[0]=='abandon') {
		if ($valeur=='oui') {return $f_sql .' = true';};
		return $f_sql .' = false';
	}; 
	
	if (($type[0]=='service') or ($type[0]=='pilotage_ddtm')) {
		$valeur= str_replace('\"','"', $valeur);
		}
			
	//$valeur= str_replace("\\r\\n","\r\n", $valeur);

		
	return $f_sql ." = '". $valeur."'";
}

$champ = array(
	dreal,
	bop,
	service,
	pilotage_ddtm,
	libelle,
	commentaires,
	date_maj,
	ligne_budgetaire_lb,
	date_demande_lb_n1,
	date_demande_lb_n,
	date_autorisation_lb,
	date_devis_lb,
	date_engagement_lb,
	date_facturation_lb,
	montant_demande_lb_n1,
	montant_demande_lb_n,
	montant_autorisation_lb,
	montant_devis_lb,
	montant_engagement_lb,
	montant_facturation_lb,
	ligne_budgetaire_lb2,
	date_demande_lb2_n1,
	date_demande_lb2_n,
	date_autorisation_lb2,
	date_devis_lb2,
	date_engagement_lb2,
	date_facturation_lb2,
	montant_demande_lb2_n1,
	montant_demande_lb2_n,
	montant_autorisation_lb2,
	montant_devis_lb2,
	montant_engagement_lb2,
	montant_facturation_lb2,
	date_demande_devis,
	date_reception_devis,
	date_verification_devis,
	date_notification_devis,
	date_debut_etude,
	date_fin_etude,
	date_avancement_etude,
	date_demande_facture,
	date_reception_facture,
	date_transmission_facture,
	date_acquittee_facture,
	annee_pgm,
	pourcentage_avancement_etude,
	montant_demande_lb,
	montant_demande_lb2,
	date_demande_lb,
	date_demande_lb2,
	valorisation_comment,
	valorisation_url,
	nom_bureau_etude,
	contact_bureau_etude,
	ref_devis,
	abandon
) ;

$majliste= array(
	libelle,
	service,
	pilotage_ddtm,
	annee_pgm,
	montant_autorisation_lb,
	montant_engagement_lb
) ;

$sql ='';
$scriptmajliste='';

foreach($champ as $element) {
	if (isset($_POST[$element])) {
		//if ($_POST[$element]<>'') {
	    	if ($sql!=''){$sql=$sql.', ';}
			$sql= $sql. formater_sql($element) ;
		//};
	};
};

$id_etude_hn = $_POST[id_etude_hn];

foreach($majliste as $element) {
	if (isset($_POST[$element])) {
		$scriptmajliste=$scriptmajliste . 'parent.leftFrame.document.getElementById("'.$id_etude_hn.'-'.$element.'").innerHTML="'.str_replace("\r\n","<br>", $_POST[$element]).'&nbsp;";' ;
	};
};

if (isset($_POST['prec'])) {
	if ($_POST['prec']<>'') {
		$prec=$_POST['prec'] ;}
		};
if (isset($_POST['suiv'])) {
	if ($_POST['suiv']<>'') {
		$suiv=$_POST['suiv'] ;}
		};
//------------- Connexion au serveur POSTGRESQL
try
	{
		$connexion = new PDO ($dsn, $user, $mdp);
	}
	catch (PDOExeption $dbex)
	{
	die ("Erreur de connexion : ".$dbex ->getMessage() );
	};

//------------- Lecture de la table des fichiers déposés sur le serveur
$nomtable='suivi_etudes.liste_etudes';

$requete1  = 'update ' . $nomtable . ' SET '. $sql .' WHERE id_etude_hn = '.$_POST[id_etude_hn] ;


  try
  {
    $result = $connexion->prepare($requete1) ;
	$result->execute();
   }
  catch (PDOException $e) 
  {
  print $e->getMessage();
  } ;

//------------- Suppression des connexions au serveur POSTGRESQL
 $connexion=NULL;

?>


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>GLPI Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8 ">
</head>
<body bgcolor="#FFFFFF">
<script language="JavaScript">
	<?php echo $scriptmajliste ; ?>
	parent.mainFrame.location.href='<?php echo 'suivi_etudes_hn_maj.php?id_etude_hn='.$_POST[id_etude_hn].'&prec='.$prec.'&suiv='.$suiv ; ?>';
	//parent.leftFrame.location.reload(true);
	
	//log.submit();
	// onload ="log.submit()"
</script>
<?php echo 'pb de mise à jour'; ?>
<br>
variable script : <br>
<?php echo $scriptmajliste ?><br>
variable SQL : <br>
<?php echo $requete1 ?>
</body>
</html>