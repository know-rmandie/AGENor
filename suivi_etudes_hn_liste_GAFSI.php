<?php require_once("etudes_fonctions.php"); ?>
<?php require_once("param_server.php"); ?>
<?php
// mise à jour septembre 2013
// paramètres
$date="";
$month="";
$day="";
$year="";
$autre="";

function formater_date($valeur) {
	$fdate = explode(' ',$valeur);
 	$fdate = explode('-',$fdate[0]);
 	return $fdate[2].'-'.$fdate[1].'-'.$fdate[0];
};

$Tab_LB = array(
	'' => 'Pas de financement',
	'T3'=>'TITRE III',
	'T5'=>'TITRE V',
	'T6'=>'TITRE VI',
	'T9'=>'TITRE IX'
	);
$Tab_Avancement_etude = array(
	"idee"=>"idée d'étude", 
	"projet"=>"projet d'étude, financement demandé",
	"programme"=>"étude programmée",
	"encours"=>"étude en cours",
	"termine"=>"étude terminée",
	"valorise"=>"étude valorisée"
	);
//------------- lecture des variables POST
$sql_service='';
$sql_bop='';
$sql_pilotage='';
$and_or1='';
$and_or2='';
$where='';

$filtre1='tous';
$filtre2='tous';
$filtre3='tous';

if (isset($_POST['dreal'])) {
	if ($_POST['dreal'] <>'') {
		$sql_service= "lower(\"dreal\") like lower('%" .$_POST['dreal']. "%')";
		//$and_or1=' OR ';
		$where=' WHERE ';
		$filtre1=$_POST['dreal']; }
		}
if (isset($_POST['bop'])) {
	if ($_POST['bop']<>'') {
		$sql_bop= "lower(\"bop\") like lower('%" .$_POST['bop']. "%')";
		//$and_or2=' OR ';
		$where=' WHERE ';
		$filtre2=$_POST['bop']; }
		}
		//"(lower(\"mots_clefs_fichier\") like lower('%" .$_POST['mots_clefs']. "%'))";

if (isset($_POST['pilotage_ddtm'])) {
	if ($_POST['pilotage_ddtm']<>'') {
		$sql_pilotage= "lower(\"pilotage_ddtm\") like lower('%" .$_POST['pilotage_ddtm']. "%')";
		$where=' WHERE ';
		$filtre3=$_POST['pilotage_ddtm']; }
		}
		
if ($filtre1<>'tous') {
	if ($filtre2<>'tous') {$and_or1=' OR ';};
	if ($filtre3<>'tous') {$and_or2=' OR ';};
		}
		else {
			if (($filtre2<>'tous') and ($filtre3<>'tous')){$and_or2=' OR ';};
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
$nomtable='suivi_etudes.liste_etudes';
$requete1  = 'select * from ' . $nomtable . $where .$sql_service.$and_or1.$sql_bop.$and_or2.$sql_pilotage.'  ORDER BY dreal, bop, ligne_budgetaire_lb, libelle';
$liste_id='0';
$bop='';
$dreal='';
$ligne_budgetaire_lb='';
// ' onclick="parent.mainFrame.location.href=\'suivi_etudes_hn_maj.php?id_etude_hn='.$row['id_etude_hn'].'\';">'
  try {
    $result = $connexion->prepare($requete1) ;
	$result->execute();
	couleurligne(0);
	$total_programme=0;
	$total_engage=0;
	
	$date2= new DateTime;
	//$date3=clone $date2;
	$date2->modify("-1 months");
	$date = $date2->format("Y-m-d").' 00:00:00';
	
	$affichage_fichier = '<table width="100%" border="1" align="center" cellpadding="4" cellspacing="0" bordercolor="#eeeeee" class="texteP">';
	$affichage_fichier = $affichage_fichier .'<th align="center" style="font-weight:bold" colspan=8 id="ReportFooter">Filtres actifs : [ Service dreal = '. $filtre1. ' ]   ou [ bop  = '.$filtre2. ' ] ou  [ Pilote DDTM = '.$filtre3. ' ]</th>';
	$affichage_fichier = $affichage_fichier .'<tr align="center" style="font-weight:bold"><td width="94%" colspan = 3 >Maître d\'ouvrage DREAL - BOP - Ligne Budgétaire <br/> date de mise à jour - Libellé - commentaires</td><td width="2%">Service</td><td width="2%">Pilotage DDTM</td><td width="2%">Année prog.</td><td width="2%">Montant programmé</td><td width="2%">Montant Engagé</td></tr>';
    while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$id_etude_hn = $row['id_etude_hn'];
		$liste_id = $liste_id . ','. $id_etude_hn;
		$ligne='';
		if (($dreal!=$row['dreal']) OR ($bop !=$row['bop']) OR ($ligne_budgetaire_lb !=$row[ligne_budgetaire_lb]) ) {
			$dreal=$row['dreal'];
			$bop =$row['bop'];
			$ligne_budgetaire_lb =$row['ligne_budgetaire_lb'] ;
			$affichage_fichier= str_replace("[total_programme]",number_format($total_programme, 2, '.', ' '), $affichage_fichier);
			$affichage_fichier= str_replace("[total_engage]",number_format($total_engage, 2, '.', ' '), $affichage_fichier);
			$total_programme=0;
			$total_engage=0;
			$ligne ='<tr style="font-weight:bold">';
			$ligne =$ligne.'<td colspan=6>'.$row['dreal'].'&nbsp;-&nbsp;'.$row['bop'].'&nbsp;-&nbsp;'.$Tab_LB[$row['ligne_budgetaire_lb']].'&nbsp;</td>';
			$ligne =$ligne.'<td style="TEXT-ALIGN:Right">[total_programme]</td>';
			$ligne =$ligne.'<td style="TEXT-ALIGN:Right">[total_engage]</td>';
			$ligne =$ligne.'</tr>';
			}
		$ligne =$ligne.'<tr '.couleurligne(1).' onclick="javascript:gotopage('.$id_etude_hn.');">';
		$ligne =$ligne.'<td width="0%">&nbsp;</td>';
		$initcouleur ='idee';
		$initcouleur = (($row['montant_demande_lb'] >0) || ($row['montant_demande_lb2'] >0)) ? 'projet'  : $initcouleur ;
		$initcouleur = (($row['montant_autorisation_lb'] >0) || ($row['montant_autorisation_lb2'] >0)) ? 'programme'  : $initcouleur ;
		$initcouleur = (($row['montant_engagement_lb'] >0) || ($row['montant_engagement_lb2'] >0)) ? 'encours'  : $initcouleur ;
		$initcouleur = (($row['pourcentage_avancement_etude']==100) && ($row['valorisation_comment']=='') && ($row['valorisation_url']=='')) ?  'termine'  : $initcouleur ;
		$initcouleur = (($row['pourcentage_avancement_etude']==100) &&(($row['valorisation_comment']!='') || ($row['valorisation_url']!=''))) ?  'valorise'  : $initcouleur ;
		if ($row['date_maj'] > $date) {
			$ligne =$ligne.'<td colspan=2 width="94%" id="'.$id_etude_hn.'-libelle"><strong>'.formater_date($row['date_maj']).' - '. $row['libelle'].'</strong>&nbsp;<br/>'.$Tab_Avancement_etude[$initcouleur].' <br/><br/> '. str_replace("\n","<br/>",$row['commentaires']).'</td>';
			}
			else
			{
			$ligne =$ligne.'<td colspan=2 width="94%" id="'.$id_etude_hn.'-libelle">'.formater_date($row['date_maj']).' - '. $row['libelle'].'&nbsp;<br/><br/>'.str_replace("\n","<br/>",$row['commentaires']).' <br/><br/> '.$Tab_Avancement_etude[$initcouleur].'</td>';
			}
		$ligne =$ligne.'<td width="2%" id="'.$id_etude_hn.'-service">'.$row['service'].'&nbsp;</td>';
		$ligne =$ligne.'<td width="2%" id="'.$id_etude_hn.'-pilotage_ddtm">'.$row['pilotage_ddtm'].'&nbsp;</td>';
		$ligne =$ligne.'<td width="2%" id="'.$id_etude_hn.'-annee_pgm">'.$row['annee_pgm'].'&nbsp;</td>';
		$ligne =$ligne.'<td width="2%" style="TEXT-ALIGN:Right" id="'.$id_etude_hn.'-montant_autorisation_lb">'.$row['montant_autorisation_lb'].'&nbsp;</td>'; // +$row['montant_autorisation_lb2']
		$ligne =$ligne.'<td width="2%" style="TEXT-ALIGN:Right" id="'.$id_etude_hn.'-montant_engagement_lb">'.$row['montant_engagement_lb'].'&nbsp;</td>';//+$row['montant_engagement_lb2']
		$ligne =$ligne.'</tr>';
		$total_programme=$total_programme + $row['montant_autorisation_lb'];
		$total_engage=$total_engage + $row['montant_engagement_lb'];

		$affichage_fichier=$affichage_fichier.$ligne;
    }
	$affichage_fichier= str_replace("[total_programme]",number_format($total_programme, 2, '.', ' '), $affichage_fichier);
	$affichage_fichier= str_replace("[total_engage]",number_format($total_engage, 2, '.', ' '), $affichage_fichier);
    $affichage_fichier = $affichage_fichier .'<tr align="center" style="font-weight:bold"><td colspan=8 id="ReportFooter"><a href="mailto:ddtm-mct@seine-maritime.gouv.fr">MCT - février 2013</a></td></tr>';
	$affichage_fichier = $affichage_fichier.'<tr"><td colspan=5>'.$requete1.'</td></tr>';
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
<link href="etudes.css" rel="stylesheet" type="text/css">
<style>
        html {
            overflow-x: hidden;
        }
    </style>

<script language="JavaScript" type="text/JavaScript">
<!--

function gotopage(id) {
	var liste = new Array (<?php echo $liste_id ?>);
	prec = 0 ;
	suiv = 0 ;
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
	//parent.topFrame.document.getElementById("Tliste").value='?id_etude_hn='+id+'&prec='+prec+'&suiv='+suiv;
} ;

//-->
</script>
</head>
<body>
<div id="Layer1" style="position:absolute; left:5px; top:4px; margin-right: 5px; width:100%; z-index:2"> 
 <form name="recherche" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST" class="texteP8">
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center" bordercolor="#eeeeee" class="texteP8">
	<tr>
	<td colspan=7>
	  	<input type="Submit" value="Filtrer sur :" name="lookfor"> 
	  	bop : <input name="bop" type="text" id="bop" size="8" maxlength="100" value="">
		Pil.DDTM : <input name="pilotage_ddtm" type="text" id="pilotage_ddtm" size="8" maxlength="100" value="">
		Année prog. : 
          <input name="annee_pgm" type="text" id="annee_pgm" value="" size="4">
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Les libellés des études modifiées depuis le <?php echo $date2->format("d-m-Y");?> sont en gras
	</td></tr>
		<tr>
		<td >service DREAL : &nbsp;</td>
		<td><input type="Submit" value="Tous" name="Tous" size="10" style="width:55px"> </td>
		<td ><input type="Submit" value="SECLAD" name="dreal" style="width:55px"> </td>
		<td><input type="Submit" value="STDMI" name="dreal" style="width:55px"> </td>
		<td><input type="Submit" value="SRE" name="dreal" style="width:55px"> </td>
		<td><input type="Submit" value="SRI" name="dreal" style="width:55px"> </td>
		<td><input type="Submit" value="SSTR" name="dreal" style="width:55px"> </td>
	</tr>
</table>
	</form>
</div>
<div id="Layer2" style="position:absolute; left:5px; top:50px;  margin-right: 5px;  width:100%; z-index:3"> 
<?php
echo $affichage_fichier;
?>
</div>

</body>
</html>
