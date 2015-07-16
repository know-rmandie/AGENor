<?php require_once("etudes_fonctions.php"); ?>
<?php require_once("param_server.php"); ?>
<?php
// mise à jour septembre 2013

//------------- Connexion au serveur POSTGRESQL
try
	{
		$connexion = new PDO ($dsn, $user, $mdp);
	}
	catch (PDOExeption $dbex)
	{
	die ("Erreur de connexion : ".$dbex ->getMessage() );
	}

// paramètres
$date="";
$Fn_completePHP ="";

$Tab_Avancement_etude = array(
	"idee"=>"idée d'étude", 
	"projet"=>"projet d'étude, financement demandé",
	"programme"=>"étude programmée",
	"encours"=>"étude en cours",
	"termine"=>"étude terminée",
	"valorise"=>"étude valorisée",
	"abandon"=>"etude abandonnée"
	);

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
	
$Tab_action_BOP = array();
$Tab_gestionnaire = array();
try {
    $sql_bop = 'select * from suivi_etudes.bop order by gestionnaire_bop, sigle_bop, action_bop';
	$result = $connexion->prepare($sql_bop) ;
	$result->execute();
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$Tab_action_BOP[$row['action_bop']] = $row['action_bop']; 
		$Tab_gestionnaire[$row['action_bop']] =  $row['gestionnaire_bop']; 
		}
	}
catch (PDOException $e) {
    print $e->getMessage();
	}

$Tab_LB = array(
	'' => 'Pas de financement',
	'T3'=>'TITRE III',
	'T5'=>'TITRE V',
	'T6'=>'TITRE VI',
	'T9'=>'TITRE IX'
	);

function formater_date($valeur) {
	$fdate = explode(' ',$valeur);
 	$fdate = explode('-',$fdate[0]);
 	return $fdate[2].'-'.$fdate[1].'-'.$fdate[0];
};

// ----------------------- nouvelle version filtrage -----------------------------------

$filtre = array(
	'tout'=>'Fiches sélectionnées',
	'dreal'=>'Gestionnaire BOP',
	'bop'=> 'BOP',
	'annee_pgm'=>'année de pgm'
	);
	
$libre_requete = array (	
    'pilotage_ddtm'=> 'Pilote DDTM',
	'service'=>'Service',
	'nom_bureau_etude'=>'Nom du BE',
	'contact_bureau_etude'=>'Contact BE',
	'libelle'=>'Libellé'
	);

$requete_sql='';
$filtre_encours='';

foreach($filtre as $nom_filtre=>$nom_lib_filtre) {
	$nom_filtre_partiel='';
	$requete_initiale='';	
	if ($_POST[$nom_filtre]<>'') {
		if (($_POST[$nom_filtre]=='*') || ($_POST[$nom_filtre]=='Tout')) {
			$requete_initiale = " 1=1 " ;
			$nom_filtre_partiel = $nom_lib_filtre . ' = toutes les fiches' ;
			}
			else
			{
			$requete_initiale = "lower(\"".$nom_filtre."\") like lower('%" .$_POST[$nom_filtre]. "%')";
			$nom_filtre_partiel = $nom_lib_filtre . ' = ' . $_POST[$nom_filtre] ;
			}
		if ($requete_sql==''){
			$requete_sql = $requete_initiale ;
			$filtre_encours= 'Filtre actif : '. $nom_filtre_partiel ;
			}
			else
			{
			$requete_sql = $requete_sql . " AND " . $requete_initiale ; 
			$filtre_encours= $filtre_encours . ' ET ' . $nom_filtre_partiel ;
			$filtre_encours= str_replace("Filtre actif","Filtres actifs", $filtre_encours);
			}
		$where=' WHERE ';
		
	};
};

if ($_POST['champ_libre']<>''){
	$nom_filtre_partiel='';
	$nom_filtre_libre='';
	$requete_initiale='';	
	$requete_libre='';	
	$champ_libre=$_POST['champ_libre'];
	$champ_libre=explode(',',$champ_libre);
	$ou_filtre_libre='';
	$or_filtre_libre='';
	foreach ($champ_libre as $valeur_cherchee) {
		$nom_filtre_partiel= $valeur_cherchee ;
		$ou=' dans ';
		$or='';
		$requete_initiale='';
		foreach ($libre_requete as $valeur_champ=>$lib_champ) {
			$requete_initiale = $requete_initiale . $or. "lower(\"".$valeur_champ."\") like lower('%" .str_replace(' ','',$valeur_cherchee). "%')";
			$nom_filtre_partiel = $nom_filtre_partiel . $ou . $lib_champ ;
			$ou = ' ou ';
			$or = ' OR ';
			}
		$nom_filtre_partiel = ($nom_filtre_partiel=='') ? '': '('. $nom_filtre_partiel .')';
		$nom_filtre_libre = $nom_filtre_libre . $ou_filtre_libre . $nom_filtre_partiel ;
		$ou_filtre_libre = " OU " ;
		$requete_initiale = ($requete_initiale=='') ? '': '( '.$requete_initiale.')';
		$requete_libre = $requete_libre . $or_filtre_libre . $requete_initiale ;
		$or_filtre_libre = " OR "; 
		}
	$nom_filtre_libre = ($nom_filtre_libre=='')? '': '( '.$nom_filtre_libre.')';
	$requete_libre = ($requete_libre=='')? '': '( '.$requete_libre.')';
	if ($requete_sql==''){
		$requete_sql = $requete_libre ;
		$filtre_encours= 'Filtre actif : '. $nom_filtre_libre ;
		}
		else
		{
		$requete_sql = $requete_sql . " AND " . $requete_libre ; 
		$filtre_encours= $filtre_encours . ' ET ' . $nom_filtre_libre ;
		$filtre_encours= str_replace("Filtre actif","Filtres actifs", $filtre_encours);
		}
	$where=' WHERE ';	
	};

//------------- Lecture de la table des fichiers déposés sur le serveur
$nomtable='suivi_etudes.liste_etudes';
$nomtabledotation='suivi_etudes.dotation';

	$requete1  = 'select * from ' ;
	$requete1 .= ' (select * from ' . $nomtable . ' [controle_date]) as t1' ;
	$requete1 .= ' LEFT OUTER JOIN ';
	$requete1 .= ' (select annee_dotation, sigle_bop_dotation, titre_dotation, sum(montant_dotation) as  montant_dotation from ' . $nomtabledotation ;
	$requete1 .=  ' [controle_date2] group by annee_dotation, sigle_bop_dotation, titre_dotation) as t2'; 
	//$requete1 .=  '(select annee_dotation, sigle_bop_dotation, titre_dotation, sum(montant_dotation) as  from montant_dotation' . $nomtabledotation . ' as t2';
	$requete1 .= ' ON ';
	$requete1 .= ' t1.bop = t2.sigle_bop_dotation AND ' ;
	$requete1 .= ' t1.annee_pgm = t2.annee_dotation AND' ;
	$requete1 .= ' t1.ligne_budgetaire_lb = t2.titre_dotation';
	$requete1 .= ' ORDER BY t1.dreal, t1.bop ASC, t1.ligne_budgetaire_lb, t1.annee_pgm DESC,t1.libelle';
	
if ($where=='') {
	$requete1 = str_replace("[controle_date]","WHERE  annee_pgm='".date("Y")."'",$requete1);
	$requete1 = str_replace("[controle_date2]","WHERE  annee_dotation='".date("Y")."'",$requete1);
	//$requete1  = 'select * from ' . $nomtable . ' WHERE  annee_pgm=\''.date("Y").'\' ORDER BY dreal, bop, ligne_budgetaire_lb, libelle';
	$filtre_encours= 'Filtre actif : année de pgm = ' . date("Y") ;
	$lib_dotation = 'Dotation '. date("Y") ." : " ;
	}
	else
	{
	$requete1 = str_replace("[controle_date]",$where.$requete_sql,$requete1);
	$requete1 = str_replace("[controle_date2]","WHERE annee_dotation = '".$_POST[annee_pgm]."'",$requete1);
	$lib_dotation = 'Dotation '. date("Y") ." : " ;
	//$lib_dotation = 'Dotation cumulée toutes années :';
	//$requete1  = 'select * from ' . $nomtable . $where .$requete_sql.'  ORDER BY dreal, bop, ligne_budgetaire_lb, libelle';
	//$req_dotation = 'select sigle_bop_dotation, titre_dotation, sum(montant_dotation) as dotation_encours ';
	//$req_dotation .= 'from ' .$nomtabledotation. ' group by sigle_bop_dotation, titre_dotation';	
	}
$liste_id='0';
$bop='';
$dreal='';
$ligne_budgetaire_lb='';

	$date1= new DateTime;
	$date1->modify("-1 months");
	$date = $date1->format("Y-m-d").' 00:00:00';
	$id_init='';
  try {
  	//$dotation = $connexion->prepare($req_dotation) ;
	//$dotation->execute();
	
    $result = $connexion->prepare($requete1) ;
	$result->execute();
	couleurligne('0');
	$total_programme=0;
	$total_engage=0;
	$total_demande=0;
	$affichage_fichier = '<table id="tabEtudes" cellpadding="4" cellspacing="0" class="texteP">';
	$affichage_fichier = $affichage_fichier .'<th colspan=9 class="ReportFooter">'. $filtre_encours .' </th>';
	$affichage_fichier = $affichage_fichier .'<tr><td colspan = 3 >Libellé</td><td >Service</td><td>Pilotage DDTM</td><td>Année prog.</td><td>Montant demandé</td><td>Montant programmé</td><td>Montant Engagé</td></tr>';
    while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$id_etude_hn = $row['id_etude_hn'];
		$id_init = ($id_init=='')? $id_etude_hn : $id_init;
		$liste_id = $liste_id . ','. $id_etude_hn;
		$ligne='';
		if (($dreal!=$row['dreal']) OR ($bop !=$row['bop']) OR ($ligne_budgetaire_lb !=$row[ligne_budgetaire_lb]) ) {
			$dreal=$row['dreal'];
			$bop =$row['bop']; 
			$dotation_en_cours =$row['montant_dotation']; 
			$ligne_budgetaire_lb =$row['ligne_budgetaire_lb'] ;
			$affiche_dotation =(($dotation_en_cours !=0)) ? $lib_dotation . number_format($dotation_en_cours, 0, '.', ' ') : "";
			$affichage_fichier= str_replace("[total_programme]",number_format($total_programme, 0, '.', ' '), $affichage_fichier);
			$affichage_fichier= str_replace("[total_engage]",number_format($total_engage, 0, '.', ' '), $affichage_fichier);
			$affichage_fichier= str_replace("[total_demande]",number_format($total_demande, 0, '.', ' '), $affichage_fichier);
			$total_programme=0;
			$total_engage=0;
			$total_demande=0;
			$ligne ='<tr class="groupe">';
			$ligne =$ligne.'<td align="center">></td>';
			$ligne =$ligne.'<td align="left">'.$row['dreal'].'&nbsp;-&nbsp;'.$row['bop'].'&nbsp;-&nbsp;'.$Tab_LB[$row['ligne_budgetaire_lb']].'&nbsp;</td>';
			$ligne =$ligne.'<td colspan=4 align="left">'.$affiche_dotation.'</td>';
			//$ligne =$ligne.'<td align="right">[total_dotation]</td><td></td>';
			$ligne =$ligne.'<td align="right">[total_demande]</td>';
			$ligne =$ligne.'<td align="right">[total_programme]</td>';
			$ligne =$ligne.'<td align="right">[total_engage]</td>';
			$ligne =$ligne.'</tr>';
			}
			// 		valeur en entrée : 0, idee, projet, programme, encours, terminee, valorisee
			// 		tr.lip {background-color:#fff;}
    		// 		tr.lii {background-color:#eee;}
			// 		idée d'étude (=étude qui n'a pas de montant renseigné, ni de valorisation) > tr.idee
			// 		projet d'étude (=étude qui a un montant demandé non vide, et aucun autre) > tr.projet
			// 		étude programmée (=étude qui a un montant autorisé non vide et pas de montant engagé) > tr.programme
			// 		étude en cours (=étude qui a un montant engagé non vide) > tr.encours
			// 		étude terminée (=étude qui est à 100% mais pas valorisée) > tr.terminee
			// 		étude valorisée (=étude à 100% et valorisé est non vide) > tr.valorisee
		$initcouleur ='idee';
		$initcouleur = (($row['montant_demande_lb'] >0) || ($row['montant_demande_lb2'] >0)) ? 'projet'  : $initcouleur ;
		$initcouleur = (($row['montant_autorisation_lb'] >0) || ($row['montant_autorisation_lb2'] >0)) ? 'programme'  : $initcouleur ;
		$initcouleur = (($row['montant_engagement_lb'] >0) || ($row['montant_engagement_lb2'] >0)) ? 'encours'  : $initcouleur ;
		$initcouleur = (($row['pourcentage_avancement_etude']==100) && ($row['valorisation_comment']=='') && ($row['valorisation_url']=='')) ?  'termine'  : $initcouleur ;
		$initcouleur = (($row['pourcentage_avancement_etude']==100) &&(($row['valorisation_comment']!='') || ($row['valorisation_url']!=''))) ?  'valorise'  : $initcouleur ;
		$initcouleur = ($row['abandon']==true) ?  'abandon'  : $initcouleur ;
		$ligne =$ligne.'<tr '.couleurligne($initcouleur).' title = "'.$Tab_Avancement_etude[$initcouleur].'">';
		$valorisation = ($row['valorisation_url']<>'') ? 'onclick="javascript:voir_valorisation(\''.$row['valorisation_url'].'\');"' : '';
		if ($row['date_maj'] > $date) {
			$ligne =$ligne.'<td id="'.$id_etude_hn.'-focus" ' . $valorisation . ' title="Mise à jour le '.formater_date($row['date_maj']).'">X</td>';
			}
			else
			{
			$ligne =$ligne.'<td id="'.$id_etude_hn.'-focus" ' . $valorisation . ' >&nbsp;</td>';
			}
		$ligne =$ligne.'<td colspan=2 id="'.$id_etude_hn.'-libelle" onclick="javascript:gotopage('.$id_etude_hn.');">'.str_replace("\r\n","<br>", $row['libelle']).'&nbsp;</td>';
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-service">'.$row['service'].'&nbsp;</td>';
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-pilotage_ddtm">'.$row['pilotage_ddtm'].'&nbsp;</td>';
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-annee_pgm">'.$row['annee_pgm'].'&nbsp;</td>';
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-montant_demande_lb">'.number_format($row['montant_demande_lb'], 0, '.', ' ').'</td>'; 
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-montant_autorisation_lb">'.number_format($row['montant_autorisation_lb'], 0, '.', ' ').'</td>'; // +$row['montant_autorisation_lb2']
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-montant_engagement_lb">'.number_format($row['montant_engagement_lb'], 0, '.', ' ').'</td>';//+$row['montant_engagement_lb2']
		$ligne =$ligne.'</tr>';
		$total_programme=$total_programme + $row['montant_autorisation_lb'];
		$total_engage=$total_engage + $row['montant_engagement_lb'];
		$total_demande=$total_demande + $row['montant_demande_lb'];
		$affichage_fichier=$affichage_fichier.$ligne;
    }
	$affichage_fichier= str_replace("[total_dotation]",number_format($dotation_en_cours, 0, '.', ' '), $affichage_fichier);
	$affichage_fichier= str_replace("[total_programme]",number_format($total_programme, 0, '.', ' '), $affichage_fichier);
	$affichage_fichier= str_replace("[total_engage]",number_format($total_engage, 0, '.', ' '), $affichage_fichier);
    $affichage_fichier= str_replace("[total_demande]",number_format($total_demande, 0, '.', ' '), $affichage_fichier);
    $affichage_fichier = $affichage_fichier .'<tr><td colspan=9 class="ReportFooter"><a href="mailto:ddtm-mct@seine-maritime.gouv.fr">MCT - Septembre 2013</a></td></tr>';
	$affichage_fichier = $affichage_fichier.'<tr><td colspan=7>'. $filtre_encours .'</td>';
	$affichage_fichier = $affichage_fichier.'<td colspan=2><input name="button_excel" type="button" id="button_excel" value="Export xls" onclick="javascript:export_excel();" title="Lire avec CALC ou Excel en format UTF8 avec séparateur virgule"></td></tr>';
	$affichage_fichier = $affichage_fichier.'</table>';
  }
  catch (PDOException $e) {
    print $e->getMessage();
  }
  
//------------- Suppression des connexions au serveur POSTGRESQL
// https://googledrive.com/host/0B1_dNrgCLl6RMVpEVjVfeDMxTnc/etudes.css

if (isset($_GET['new_fiche'])) {
	if($_GET['new_fiche']<>'') {$id_init=$_GET['new_fiche'];};
	};
	
$connexion=NULL;
?>
<html>
<head>
<title>Liste des études - version septembre 2013</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="etudes.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
<!--

function gotopage(id) {
	var liste = new Array (<?php echo $liste_id ?>);
	prec = 0 ;
	suiv = 0 ;
	//getelementbyId
	parent.topFrame.document.getElementById("id_init").value=id;
	switch(id) {
		case liste[1]:
			prec = liste[liste.length-1];
			if (liste.length >2) {
				suiv = liste[2];
				}
				else
				{
				suiv = liste[1];
				}
  			break;
		case liste[liste.length-1]:
  			prec = liste[liste.length-2];
			suiv = liste[1];
  			break;
		default:
  			for (i = 2; i < liste.length-1 ; i++){ 
				if (id==liste[i]) {
					prec = liste[i-1];
					suiv = liste[i+1];
					};
				}
  			break;
		};
	parent.mainFrame.location.href='suivi_etudes_hn_maj.php?id_etude_hn='+id+'&prec='+prec+'&suiv='+suiv;
} ;

function export_excel() {
	excel_form.submit();
}

function voir_valorisation(lien) {
	window.open(lien,"","menubar=yes, status=yes, scrollbars=yes, menubar=yes, width=800, height=500");
}

gotopage('<?php echo $id_init ; ?>');
//-->
</script>
</head>
<body>
<form name="recherche" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST" class="texteP8">
<table cellspacing="0" cellpadding="1" class="texteP8">
	<tr>
	<td colspan=8>
	  	<input type="Submit" value="Filtrer sur :" name="lookfor" class="service"> 
	  	BOP : <select name="bop" size="1" id="bop" >
				<?php
				$gestionnaire='';
				$fin_optgroup='';
				echo '<option value="" selected>Liste des BOP</option>'."\n" ;
				foreach($Tab_action_BOP AS $cle => $valeur) {
					if ($gestionnaire!=$Tab_gestionnaire[$cle]) {
						$gestionnaire = $Tab_gestionnaire[$cle];
						echo $fin_optgroup;
						$fin_optgroup='</optgroup>';
						echo '<optgroup label="'. $gestionnaire .'">'."\n" ;
						} 
					$selected=($cle=='') ? ' selected':'';
					echo '<option value="'. $cle . '"'. $selected . '>    '. $valeur .'</option>'."\n" ;
					};
				echo $fin_optgroup;
				?>
      		  </select>
		Année prog. : <select name="annee_pgm" size="1" id="annee_pgm" >
				<?php
				foreach($Tab_Annee AS $cle => $valeur) {
					$selected=($cle=='') ? ' selected':'';
					echo '<option value="'. $cle . '"'. $selected . '>'. $valeur .'</option>'."\n" ;
					};
				?>
        	</select>	
		Autre recherche : <input name="champ_libre" type="text" id="champ_libre" size="8" maxlength="100" value="" title="Mots complets ou partiel dans Service, Pilote, nom BE, nom contact ou libellé.. si plusieurs, les séparer par une virgule">
		  <input name="setfocus" type="hidden" id="setfocus" value="">
          <input name="setcolor" type="hidden" id="setcolor" value="">
	</td></tr>
		<tr>
		<td >GBOP : &nbsp;</td>
		<td><input type="Submit" value="Tout" name="tout"> </td>
		<td ><input type="Submit" value="SECLAD" name="dreal"> </td>
		<td><input type="Submit" value="SDTMI" name="dreal"> </td>
		<td><input type="Submit" value="SRE" name="dreal"> </td>
		<td><input type="Submit" value="SRI" name="dreal"> </td>
		<td><input type="Submit" value="SSTR" name="dreal"> </td>
		<td><input type="Submit" value="Tous gestionnaires" name="dreal" title="Tous gestionnaires"> </td>
	</tr>
</table>
	</form>
<?php
echo $affichage_fichier;
?>
<table id="legende"><tr>
    <td class="idee">&nbsp;</td><td>idée d'étude</td><td>></td>
    <td class="projet">&nbsp;</td><td>projet d'étude</td><td>></td>
    <td class="programme">&nbsp;</td><td>étude programmée</td><td>></td>
    <td class="encours">&nbsp;</td><td>étude en cours</td><td>></td>
    <td class="termine">&nbsp;</td><td>étude terminée</td><td>></td>
    <td class="valorise">&nbsp;</td><td>étude valorisée</td>
    <td class="abandon">&nbsp;</td><td>étude abandonnée</td>
</tr></table> 
<form action="suivi_etudes_hn_export_xls.php" method="post" name="excel_form" target="_blank">
  <input name="excel_requete" type="hidden" id="excel_requete" value="<?php echo str_replace('"','&quot;',$requete1) ; ?>">
</form>
</body>
</html>
