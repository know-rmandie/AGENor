<?php require_once("param_server.php"); ?>
<?php
// mise à jour septembre 2013
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
	valorisation_url
) ;

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
if (isset($_POST['excel_requete'])) {
	
	$requete1  = $_POST['excel_requete'];
	//$requete1  = str_replace("%20"," ",$requete1) ;
	$requete1  = str_replace("%27","\"",$requete1) ;
	$requete1  = str_replace("\'","'",$requete1) ;
	$requete1  = str_replace('\"','"',$requete1) ;
	$requete1  = str_replace('&quot;','"',$requete1) ;
	}
	else
	{
	$requete1  = 'select * from suivi_etudes.liste_etudes ORDER BY dreal, bop, ligne_budgetaire_lb, libelle' ;
	}
$affichage_fichier='';
$ligneTitre ='';
try
	{	
	$result = $connexion->prepare($requete1) ;
	$result->execute();
	foreach($champ as $element) {
		$ligneTitre =$ligneTitre. '"'. str_replace("_"," ",$element).'",';
		}
	    
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$affichage_fichier= $affichage_fichier ."\n" ;
		$enregistrement='';
		foreach($champ as $element) {
			$enregistrement = str_replace(CHR(13).CHR(10)," - ",$row[$element]) ;
			$affichage_fichier =$affichage_fichier . '"'. $enregistrement.'",';
			}
		}
	
	$affichage_fichier = $ligneTitre . $affichage_fichier;
	}
	catch (PDOException $e) {
    	print $e->getMessage();
	} ;

header("Content-disposition: attachment; filename=suivi_etudes_hn.xls");
header('Content-Type: text/html; charset=UTF-8');
header("Content-Type: application/force-download");
header("Content-Transfer-Encoding: application/vnd.ms-excel\n");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
header("Expires: 0");

echo $affichage_fichier . '<br>';
/*echo $requete1 . '<br>';
echo$_POST['excel_requete'] . '<br>';*/
?>



