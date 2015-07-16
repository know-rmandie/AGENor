<?php 
require_once("param_server.php"); 
// mise à jour septembre 2013

//------------- Connexion au serveur POSTGRESQL
try
	{
		$connexion = new PDO ($dsn, $user, $mdp);
	}
	catch (PDOExeption $dbex)
	{
	die ("Erreur de connexion : ".$dbex ->getMessage() );
	};

// -------------- init variables ------------------
// Attention, sauvegarder le document en utf-8
// pour assurer la compatibilité avec les serveurs
// pour le passsage de caractères accentués
$Tab_Annee = array(
	''=>'',
	'2007'=>'2007',
	'2008'=>'2008',
	'2009'=>'2009',
	'2010'=>'2010',
	'2011'=>'2011',
	'2012'=>'2012',
	'2013'=>'2013',
	'2014'=>'2014',
	'2015'=>'2015'
	) ;

$Tab_BOP = array();
$Tab_gestionnaire = array();
$Tab_gest_javascript = 'var o = new Object();';
$Tab_type_bop_javascript  = 'var t = new Object();';
try {
    $sql_bop = 'select * from suivi_etudes.bop order by gestionnaire_bop, sigle_bop, action_bop';
	$result = $connexion->prepare($sql_bop) ;
	$result->execute();
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$Tab_BOP[$row['action_bop']] = $row['action_bop']; 
		$Tab_gestionnaire[$row['action_bop']] =  $row['gestionnaire_bop']; 
		$Tab_gest_javascript .='o["'.$row['action_bop'].'"] = "'.$row['gestionnaire_bop'].'";';
		$Tab_type_bop_javascript .='t["'.$row['action_bop'].'"] = "'.$row['sigle_bop'].'";';
		}
	}
catch (PDOException $e) {
    print $e->getMessage();
	} ;

$Tab_DREAL = array();
$Tab_dreal_javascript = '';
try {
    $sql_bop = 'SELECT distinct bop.gestionnaire_bop FROM suivi_etudes.bop order by bop.gestionnaire_bop;';
	$result = $connexion->prepare($sql_bop) ;
	$result->execute();
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$Tab_DREAL[$row['gestionnaire_bop']] = $row['gestionnaire_bop']; 
		$Tab_dreal_javascript .='newoption=document.createElement("option");';
		if ($row['gestionnaire_bop']=='Tous gestionnaires')
		 	{
			 $Tab_dreal_javascript .='newoption.text = "choix gestionnaire";';
			 $Tab_dreal_javascript .='newoption.defaultSelected = 1 ;';
			 }
			 else
			 {
			 $Tab_dreal_javascript .='newoption.text = "'. $row['gestionnaire_bop'].'";';
			 };
		$Tab_dreal_javascript .='newoption.value = "'. $row['gestionnaire_bop'].'";';
		$Tab_dreal_javascript .= 'document.getElementById("dreal").appendChild(newoption);';
		}
	}
catch (PDOException $e) {
    print $e->getMessage();
	} ;
	
$Tab_LB = array(
	'' => 'Pas de financement',
	'T3'=>'TITRE III',
	'T5'=>'TITRE V',
	'T6'=>'TITRE VI',
	'T9'=>'TITRE IX'
	);
	
function creation_select($tableau,$select_id,$defaut) {
	$select ='';
	$selected='';
	// suppose structure <select name="xx" size=1 id="xx"></select> créée
	//gestion_car_invisble_zone_texte($defaut);
	
	foreach($tableau AS $cle => $valeur)
     {
		 $select = $select . 'newoption=document.createElement("option");';
		 $select = $select . 'newoption.text = "'. $valeur.'";';
		 $select = $select . 'newoption.value = "'. $cle .'";';
		 if ($cle==$defaut)
		 	{
			 $select = $select .'newoption.defaultSelected = 1 ;';
			 };
		$select = $select . 'document.getElementById("'.$select_id.'").appendChild(newoption);';
		 //$select = $select . 'document.getElementById("'.$defaut.'").remove(0);';
     } 
	 return $select ;
};

function formater_client($valeur) {
	global $row ;
	$type=explode('_',$valeur);
	$valeur = $row[$valeur] ;
	
	if (empty($valeur) or ($valeur=='')) { 
		$valeur='';
		return '';}; 
	
	if ($type[0]=='date') {
		$fdate = explode(' ',$valeur);
 		$fdate = explode('-',$fdate[0]);
 		return $fdate[2].'-'.$fdate[1].'-'.$fdate[0];
	};

	if ($type[0]=='montant') {
		return number_format($valeur, 2, '.', ' ');
	};
	
	if (($type[0]=='libelle') or ($type[0]=='commentaires')) {
		//$valeur= str_replace("\r\n","\\r\\n", $valeur);
		$valeur= str_replace("  "," ", $valeur);
    	return $valeur;
	};
	
	if (($type[0]=='service') or ($type[0]=='pilotage_ddtm')) {
		$valeur= str_replace("\r\n"," ", $valeur);
		$valeur= str_replace("  "," ", $valeur);
		$valeur= str_replace('"','\"', $valeur);
    	return $valeur;
	};

	return $valeur;
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
	date_autorisation_lb,
	date_devis_lb,
	date_engagement_lb,
	date_facturation_lb,
	montant_autorisation_lb,
	montant_devis_lb,
	montant_engagement_lb,
	montant_facturation_lb,
	annee_pgm,
	date_reception_devis,
	date_verification_devis,
	date_notification_devis,
	date_debut_etude,
	date_fin_etude,
	date_demande_facture,
	date_reception_facture,
	date_transmission_facture,
	date_acquittee_facture,
	pourcentage_avancement_etude,
	date_demande_devis,
	montant_demande_lb,
	date_demande_lb,
	valorisation_comment,
	valorisation_url,
	nom_bureau_etude,
	contact_bureau_etude,
	ref_devis,
	abandon
) ;
$id_etude_hn=3;
$Fn_completePHP = '';
$affiche ='';
$prec=0;
$suiv=0;
$droit_maj='';
$send_maj='disabled';
$username='';
if (isset($_GET['id_etude_hn'])) {
	if ($_GET['id_etude_hn']<>'') {
		$id_etude_hn=$_GET['id_etude_hn'] ;}
		};
if (isset($_GET['prec'])) {
	if ($_GET['prec']<>'') {
		$prec=$_GET['prec'] ;}
		};
if (isset($_GET['suiv'])) {
	if ($_GET['suiv']<>'') {
		$suiv=$_GET['suiv'] ;}
		};

//------------- Lecture de la table des fichiers déposés sur le serveur

$nomtable='suivi_etudes.liste_etudes';
$requete1  = 'select * from ' . $nomtable . ' WHERE id_etude_hn = '.$id_etude_hn ;

  try
  {	
	$result = $connexion->prepare($requete1) ;
	$result->execute();
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		foreach($champ as $element) {
			$champformulaire = formater_client($element) ;
			$flag=0;
			if ($element=='pilotage_ddtm') {
				$pilotage_ddtm=$champformulaire;
			}
			
			if ($element=='annee_pgm') {
				$Fn_completePHP = $Fn_completePHP.creation_select($Tab_Annee,'annee_pgm',$champformulaire );
				$affiche = $affiche. $element . ' = '. $champformulaire. '<br/>';
				$flag=1;
				}
						
			if ($element=='bop') {
					$bop_selected=$champformulaire;
					$flag=1;
				}
			if ($element=='dreal') {
				$Fn_completePHP = $Fn_completePHP.creation_select($Tab_DREAL,'dreal',$champformulaire );
				$affiche = $affiche. $element . ' = '. $champformulaire. '<br/>';
				$flag=1;
				}
			if (($element=='ligne_budgetaire_lb')or ($element=='ligne_budgetaire_lb2')){
				$Fn_completePHP = $Fn_completePHP.creation_select($Tab_LB,$element,$champformulaire );
				$affiche = $affiche. $element . ' = '. $champformulaire. '<br/>';
				$flag=1;
				}
				
			if ($element=='libelle'){
				// pour les champs mémo, mise à  jour directement dans la fiche
				$libelle = $champformulaire;
				$flag=1;
				}
				
			if ($element=='commentaires'){
				// pour les champs mémo, mise à  jour directement dans la fiche
				$commentaires = $champformulaire;
				$flag=1;
				}
			
			if ($element=='date_maj'){
				// la date de mise à jour est directement sur la fiche dans le champs date_derniere_maj
				// le champs date_maj du formulaire contient la date du jour
				$date_maj = $champformulaire;
				$flag=1;
				}
				
			if ($element=='abandon'){
				// case à cocher
				$abandon_oui = "";
				$abandon_non = "checked";
				if ($champformulaire==true) {
					$abandon_oui = "checked";
					$abandon_non = "";
					}
				$flag=1;
				}
			
			if ($flag==0)  {
				$Fn_completePHP = $Fn_completePHP.'document.getElementById("'.$element.'").value="'.$champformulaire.'";';
				$affiche = $affiche. $element . ' = '. $champformulaire. '<br/>';
			}
			
			
		};
	};
  }
  catch (PDOException $e) 
  {
  print $e->getMessage();
  }

//------------- Suppression des connexions au serveur POSTGRESQL
$connexion=NULL;
?>