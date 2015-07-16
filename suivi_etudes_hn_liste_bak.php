<?php require_once("etudes_fonctions.php"); ?>
<?php require_once("param_server.php"); ?>
<?php
// paramètres
$date="";
$Fn_completePHP ="";

$Tab_Avancement_etude = array(
	"idee"=>"idée d'étude", 
	"projet"=>"projet d'étude, financement demandée",
	"programme"=>"étude programmée",
	"encours"=>"étude en cours",
	"termine"=>"étude terminée",
	"valorise"=>"étude valorisée"
	);

$Tab_Annee = array(
	''=>'',
	'2007'=>'2007',
	'2008'=>'2008',
	'2009'=>'2009',
	'2010'=>'2010',
	'2012'=>'2012',
	'2013'=>'2013',
	'2014'=>'2014',
	'2015'=>'2015'
	) ;
	
$Tab_BOP = array(
	''=>'',
	'CPPEEDM'=>'CPPEEDM',
	'Enveloppe D'=>'Enveloppe \"D\"',	
	'GUT'=>'GUT',
	'AUHP'=>'AUHP',
	'DAOL'=>'DAOL',
	'Immo Etat' =>'Immo Etat',
	'QC' =>'QC',
	'AUHP PDMI' => 'AUHP PDMI',
	'EB' => 'Eau et Biodiversité',
	'Fonds Barnier' =>'Fonds Barnier',
	'Fonds Barnier+ PR'=>'Fonds Barnier et PR',
	'PR'=>'PR',
	'SR'=>'SR'
	) ;
	
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
// filtre indice => champ, libellé affiché
// [ Service dreal = '. $filtre[0]. ' ]   ou [ bop  = '.$filtre[1]. ' ] ou  [ Pilote DDTM = '.$filtre[2]. ' ] ou [année de pgm = '. $filtre[3].' 
$filtre = array(
	'tout'=>'Fiches sélectionnées',
	'dreal'=>'Gestionnaire BOP',
	'bop'=> 'BOP',
	'pilotage_ddtm'=> 'Pilote DDTM',
	'annee_pgm'=>'année de pgm'
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

	
//------------- Connexion au serveur POSTGRESQL
try
	{
		$connexion = new PDO ($dsn, $user, $mdp);
	}
	catch (PDOExeption $dbex)
	{
	die ("Erreur de connexion : ".$dbex ->getMessage() );
	}

//------------- Lecture de la table des fichiers déposés sur le serveur
$nomtable='suivi.etudes_hn';
if ($where=='') {
	$requete1  = 'select * from ' . $nomtable . ' WHERE  annee_pgm=\''.date("Y").'\' ORDER BY dreal, bop, ligne_budgetaire_lb, libelle';
	$filtre_encours= 'Filtre actif : année de pgm = ' . date("Y") ;
	}
	else
	{
	$requete1  = 'select * from ' . $nomtable . $where .$requete_sql.'  ORDER BY dreal, bop, ligne_budgetaire_lb, libelle';
	}
$liste_id='0';
$bop='';
$dreal='';
$ligne_budgetaire_lb='';

	$date1= new DateTime;
	$date1->modify("-1 months");
	$date = $date1->format("Y-m-d").' 00:00:00';

  try {
    $result = $connexion->prepare($requete1) ;
	$result->execute();
	couleurligne('0');
	$total_programme=0;
	$total_engage=0;
	$affichage_fichier = '<table id="tabEtudes" cellpadding="4" cellspacing="0" class="texteP">';
	$affichage_fichier = $affichage_fichier .'<th colspan=8 class="ReportFooter">'. $filtre_encours .' </th>';
	$affichage_fichier = $affichage_fichier .'<tr><td colspan = 3 >Libellé</td><td >Service</td><td>Pilotage DDTM</td><td>Année prog.</td><td>Montant programmé</td><td>Montant Engagé</td></tr>';
    while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$id_etude_hn = $row['id_etude_hn'];
		$liste_id = $liste_id . ','. $id_etude_hn;
		$ligne='';
		if (($dreal!=$row['dreal']) OR ($bop !=$row['bop']) OR ($ligne_budgetaire_lb !=$row[ligne_budgetaire_lb]) ) {
			$dreal=$row['dreal'];
			$bop =$row['bop']; 
			$ligne_budgetaire_lb =$row['ligne_budgetaire_lb'] ;
			$affichage_fichier= str_replace("[total_programme]",number_format($total_programme, 0, '.', ' '), $affichage_fichier);
			$affichage_fichier= str_replace("[total_engage]",number_format($total_engage, 0, '.', ' '), $affichage_fichier);
			$total_programme=0;
			$total_engage=0;
			$ligne ='<tr class="groupe">';
			$ligne =$ligne.'<td colspan=6>'.$row['dreal'].'&nbsp;-&nbsp;'.$row['bop'].'&nbsp;-&nbsp;'.$Tab_LB[$row['ligne_budgetaire_lb']].'&nbsp;</td>';
			$ligne =$ligne.'<td>[total_programme]</td>';
			$ligne =$ligne.'<td>[total_engage]</td>';
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
		$ligne =$ligne.'<tr '.couleurligne($initcouleur).' title = "'.$Tab_Avancement_etude[$initcouleur].'">';
		if ($row['date_maj'] > $date) {
			$ligne =$ligne.'<td id="'.$id_etude_hn.'-focus" title="Mise à jour le '.formater_date($row['date_maj']).'">X</td>';
			}
			else
			{
			$ligne =$ligne.'<td id="'.$id_etude_hn.'-focus">&nbsp;</td>';
			}
		$ligne =$ligne.'<td colspan=2 id="'.$id_etude_hn.'-libelle" onclick="javascript:gotopage('.$id_etude_hn.');">'.str_replace("\r\n","<br>", $row['libelle']).'&nbsp;</td>';
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-service">'.$row['service'].'&nbsp;</td>';
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-pilotage_ddtm">'.$row['pilotage_ddtm'].'&nbsp;</td>';
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-annee_pgm">'.$row['annee_pgm'].'&nbsp;</td>';
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-montant_autorisation_lb">'.number_format($row['montant_autorisation_lb'], 0, '.', ' ').'</td>'; // +$row['montant_autorisation_lb2']
		$ligne =$ligne.'<td id="'.$id_etude_hn.'-montant_engagement_lb">'.number_format($row['montant_engagement_lb'], 0, '.', ' ').'</td>';//+$row['montant_engagement_lb2']
		$ligne =$ligne.'</tr>';
		$total_programme=$total_programme + $row['montant_autorisation_lb'];
		$total_engage=$total_engage + $row['montant_engagement_lb'];

		$affichage_fichier=$affichage_fichier.$ligne;
    }
	$affichage_fichier= str_replace("[total_programme]",number_format($total_programme, 0, '.', ' '), $affichage_fichier);
	$affichage_fichier= str_replace("[total_engage]",number_format($total_engage, 0, '.', ' '), $affichage_fichier);
    $affichage_fichier = $affichage_fichier .'<tr><td colspan=8 class="ReportFooter"><a href="mailto:ddtm-mct@seine-maritime.gouv.fr">MCT - juin 2013</a></td></tr>';
	$affichage_fichier = $affichage_fichier.'<tr><td colspan=6>'.$requete1.'</td>';
	$affichage_fichier = $affichage_fichier.'<td colspan=2><input name="button_excel" type="button" id="button_excel" value="Export xls" onclick="javascript:export_excel();" title="Lire avec CALC ou Excel en format UTF8 avec séparateur point-virgule"></td></tr>';
	$affichage_fichier = $affichage_fichier.'</table>';
  }
  catch (PDOException $e) {
    print $e->getMessage();
  }
  
//------------- Suppression des connexions au serveur POSTGRESQL
$connexion=NULL;
?>
<html>
<head>
<title>Liste des études - version de 20/02/2013</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="https://googledrive.com/host/0B1_dNrgCLl6RMVpEVjVfeDMxTnc/etudes.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
<!--

function gotopage(id) {
	var liste = new Array (<?php echo $liste_id ?>);
	prec = 0 ;
	suiv = 0 ;
	//getelementbyId
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
	//window.open("suivi_etudes_hn_export_xls.php?requete=<?php echo $requete1 ?>","excel","menubar=no, status=no, scrollbars=no, menubar=no, width=200, height=100");
	//parent.mainFrame.location.href="suivi_etudes_hn_export_xls.php?requete=<?php echo $requete1 ?>";
	excel_form.submit();
}


//-->
</script>
</head>
<body>
<form name="recherche" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST" class="texteP8">
<table cellspacing="0" cellpadding="1" class="texteP8">
	<tr>
	<td colspan=8>
	  	<input type="Submit" value="Filtrer sur :" name="lookfor" class="service"> 
	  	BOP : <select name="bop" size="1" id="bop">
				<?php
				foreach($Tab_BOP AS $cle => $valeur) {
					$selected=($cle=='') ? ' selected':'';
					echo '<option value="'. $cle . '"'. $selected . '>'. $valeur .'</option>'."\n" ;
					};
				?>
      		  </select>
		Pil.DDTM : <input name="pilotage_ddtm" type="text" id="pilotage_ddtm" size="8" maxlength="100" value="">
		Année prog. : <select name="annee_pgm" size="1" id="annee_pgm">
				<?php
				foreach($Tab_Annee AS $cle => $valeur) {
					$selected=($cle=='') ? ' selected':'';
					echo '<option value="'. $cle . '"'. $selected . '>'. $valeur .'</option>'."\n" ;
					};
				?>
        	</select>	
		  <input name="setfocus" type="hidden" id="setfocus" value="">
          <input name="setcolor" type="hidden" id="setcolor" value="">
	</td></tr>
		<tr>
		<td >GBOP : &nbsp;</td>
		<td><input type="Submit" value="Tout" name="tout"> </td>
		<td ><input type="Submit" value="SECLAD" name="dreal"> </td>
		<td><input type="Submit" value="STDMI" name="dreal"> </td>
		<td><input type="Submit" value="SRE" name="dreal"> </td>
		<td><input type="Submit" value="SRI" name="dreal"> </td>
		<td><input type="Submit" value="SSTR" name="dreal"> </td>
		<td><input type="Submit" value="Hors BOP" name="dreal"> </td>
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
</tr></table> 
<form action="suivi_etudes_hn_export_xls.php" method="post" name="excel_form" target="_blank">
  <input name="excel_requete" type="hidden" id="excel_requete" value="<?php echo str_replace('"','&quot;',$requete1) ; ?>">
</form>
</body>
</html>
