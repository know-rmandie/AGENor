<?php require_once("suivi_etudes_hn_param_maj.php"); ?>
<html>
<head>
<title>Suivi des études - mise à jour fiche - version septembre 2013</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="etudes.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

<?php
 echo $Tab_gest_javascript ;
 echo $Tab_type_bop_javascript ;
?>
 
function completePHP() {
	<?php 
	echo $Fn_completePHP ;
	?>
} ;

function active_envoi() {
	droit_envoi=parent.topFrame.document.getElementById("Tpilote").value;
	document.getElementById('utilisateur').value=parent.topFrame.document.getElementById("Tuser").value;
	chaine_reference='<?php echo $pilotage_ddtm ?>';
	chaine_bop='<?php echo $bop_selected ?>';
	ensemble_droit = droit_envoi.split(',');
	//alert(ensemble_droit.length);
	for (var i=0; i<ensemble_droit.length;i++) {
		//alert('indexOf ' + chaine_reference.indexOf(ensemble_droit[i]));
		if ((chaine_reference.indexOf(ensemble_droit[i]) >-1) || (chaine_bop.indexOf(ensemble_droit[i]) >-1)){
			//alert('OK');
			document.getElementById('Envoyer').disabled=false;
			return;
			}
			else
			{
			//alert('Non OK');
			document.getElementById('Envoyer').disabled=true;
			}
		};
	//if (droit_envoi=='') or (droit_envoi==''){document.getElementById('Envoyer').disabled=true;);
	//if (droit_envoi=='tout'){document.getElementById('Envoyer').disabled=false;);
	//alert('résultat = ' + document.getElementById('Envoyer').disabled);
};

function supprimer(id_select) {
	myselect = document.getElementById(id_select) ;
	n=myselect.length; // nombre d'options de la liste
	for (i=0;i<n;i++)
		{
		myselect[0] = null;// suppression ligne par ligne
		}
	//alert("n = " + n);
}

function choix_gestionnaire() {
 	var x=document.getElementById("bop").selectedIndex;
	var y=document.getElementById("bop").options;
	//alert("nn Index: " + y[x].index + " is " + y[x].text);
	supprimer("dreal");
	if ( y[x].index!=0 && o[y[x].text]!='Tous gestionnaires' ) {
		newoption=document.createElement("option");
		newoption.text = o[y[x].text];
		newoption.value = o[y[x].text];
		document.getElementById("dreal").appendChild(newoption);
		}
		else
		{
		<?php echo $Tab_dreal_javascript ; ?>
		}
	//alert("Index: " + y[x].index + " is " + y[x].text + " --> " + t[y[x].text]);
	if (t[y[x].text]=='Hors BOP') {
		document.getElementById("id_gestion").innerHTML = t[y[x].text] ;
		}
		else {
		document.getElementById("id_gestion").innerHTML = 'Gestionnaire BOP : ';
		}
		
}
//-->
</script>
</head>

<body onload="completePHP()">
<div id="Layer1" style="position:absolute;  z-index:1"> 
  <form action="suivi_etudes_hn_exec_maj.php" method="post" name="form1" target="_self" class="texteP" >
    <p> date de dernière mise à jour : 
      <input name="date_derniere_maj" type="text" id="date_derniere_maj" style="TEXT-ALIGN:Center" value="<?php echo $date_maj  ?>" size="9" maxlength="10"  readonly="">
      <input name="utilisateur" type="text" id="utilisateur" readonly="">
	  Service: <input name="service" type="text" id="service">
    </p>
    <p>&nbsp; </p>
    <p>BOP : 
      <select name="bop" size="1" id="bop" onChange="javascript:choix_gestionnaire();">
	  	<?php
					$select ='';
					$gestionnaire='';
					$fin_optgroup='';
					echo '<option value="" selected>Choisir dans la liste</option>'."\n" ;
					foreach($Tab_BOP AS $cle => $valeur) {
						if ($gestionnaire!=$Tab_gestionnaire[$cle]) {
							$gestionnaire = $Tab_gestionnaire[$cle];
							echo $fin_optgroup;
							$fin_optgroup='</optgroup>';
							echo '<optgroup label="'. $gestionnaire .'">' ;
							} 
					 	$selected=($cle==$bop_selected) ? ' selected':'';
						echo '<option value="'. $cle . '"'. $selected . '>    '. $valeur .'</option>'."\n" ;
						};
					echo $fin_optgroup;
		?>
      </select>
      <span id="id_gestion">Gestionnaire BOP : </span> : 
      <select name="dreal" size="1" id="dreal">
      </select>
      <input name="id_etude_hn" type="hidden" id="id_etude_hn" value="<?php echo $id_etude_hn ?>">
      <input name="date_maj" type="hidden" id="date_maj" value="<?php echo date('d-m-Y') ?>">
	  Année de prog : <select name="annee_pgm" size="1" id="annee_pgm" title="Année de programmation"></select>
    </p>
	<p>&nbsp; </p>
    <p>Pilotage Service (DDTM, DREAL): <input name="pilotage_ddtm" type="text" id="pilotage_ddtm">
       Nom BE : <input name="nom_bureau_etude" type="text" id="nom_bureau_etude" title="Bureau d'études mandaté (Sigle Division CETE, nom BE privé, ..">
	   Contact BE : <input name="contact_bureau_etude" type="text" id="contact_bureau_etude" title="Contact bureau d'études mandaté">
    </p>
    <p>&nbsp; </p>
    <table cellpadding="4" cellspacing="1" class="texteP">
      <tr>
    <td>Libellé :</td>
    <td><textarea name="libelle" cols="80%" rows="1" id="libelle"><?php echo $libelle ?></textarea></td>
  </tr>
  <tr>
    <td>Commentaire&nbsp;:</td>
    <td><textarea name="commentaires" cols="80%" rows="3" id="commentaires"><?php echo $commentaires ?></textarea></td>
  </tr>
</table>
    <h2>FINANCEMENT</h2>
<table cellpadding="5" cellspacing="0"  class="texteP" title="Date jj-mm-aaaa & Montant xxxxxxx.xx">
            <tr style="font-weight:bold"> 
              <td align="center">
				  <select name="ligne_budgetaire_lb" size="1" id="ligne_budgetaire_lb">
                  <option selected value="">Ligne Budgétaire</option>
                  </select></td>
              <td align="center">DATE <br/>
              </td>
              <td align="center">MONTANT en €</td>
            </tr>
			<tr> 
              <td>Total demandé </td>
              <td align="center" ><input name="date_demande_lb" type="text" id="date_demande_lb"  style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
              <td align="center" ><input name="montant_demande_lb" type="text" id="montant_demande_lb"  style="TEXT-ALIGN:Right" size="9" maxlength="10"></td>
            </tr>
            <tr> 
              <td>Total autorisé ou programmé</td>
              <td align="center" ><input name="date_autorisation_lb" type="text" id="date_autorisation_lb"  style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
              <td align="center">
				<input name="montant_autorisation_lb" type="text" id="montant_autorisation_lb" style="TEXT-ALIGN:Right" size="9" maxlength="10"></td>
            </tr>
            <tr> 
              <td>Total Devis Accepté</td>
              <td align="center" ><input name="date_devis_lb" type="text" id="date_devis_lb" style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
              <td align="center" ><input name="montant_devis_lb" type="text" id="montant_devis_lb" style="TEXT-ALIGN:Right" size="9" maxlength="10"></td>
            </tr>
            <tr> 
              <td>Total Engagement</td>
              <td align="center" ><input name="date_engagement_lb" type="text" id="date_engagement_lb" style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
              <td align="center" ><input name="montant_engagement_lb" type="text" id="montant_engagement_lb" style="TEXT-ALIGN:Right" size="9" maxlength="10"></td>
            </tr>
            <tr> 
              <td>Total Facturation</td>
              <td align="center" ><input name="date_facturation_lb" type="text" id="date_facturation_lb" style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
              <td align="center" ><input name="montant_facturation_lb" type="text" id="montant_facturation_lb" style="TEXT-ALIGN:Right" size="9" maxlength="10"></td>
            </tr>
    </table>
    <p>&nbsp;</p>
    <h2>AVANCEMENT DU DOSSIER </h2>
	<table cellpadding="5" cellspacing="0" class="avancement" title="Date jj-mm-aaaa">
      <tr>
        <td rowspan="3">Devis</td>
		<td >demandé le </td>
        <td><input name="date_demande_devis" type="text" id="date_demande_devis"  style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
		<td >vérifié le </td>
    	<td ><input name="date_verification_devis" type="text" id="date_verification_devis"  style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
	</tr> 
	<tr>
    	<td>reçu le </td>
   		<td><input name="date_reception_devis" type="text" id="date_reception_devis"  style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
	 	<td>accepté et notifié le </td>
    	<td><input name="date_notification_devis" type="text" id="date_notification_devis"  style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
  	</tr>
	<tr>
    	<td>Réf. devis </td>
   		<td colspan="3"><input name="ref_devis" type="text" id="ref_devis" size="20" maxlength="20"></td>
  	</tr>
	<tr>
		<td rowspan="2">Etude</td>
		<td>commencée le </td>
        <td><input name="date_debut_etude" type="text" id="date_debut_etude"  style="TEXT-ALIGN:Center" size="7" maxlength="10"></td>
        <td>pourcentage d'avancement </td>
        <td><input name="pourcentage_avancement_etude" type="text" id="pourcentage_avancement_etude" style="TEXT-ALIGN:Center" size="4" maxlength="3">%</td>
     </tr>
     <tr> 
        <td>terminée le </td>
        <td><input name="date_fin_etude" type="text" id="date_fin_etude" size="7"  style="TEXT-ALIGN:Center" maxlength="10"></td>
        <td>abandonnée </td>
        <td><input name="abandon" id="abandon" type="radio" value="oui" <?php echo $abandon_oui ?>> oui 
            <input name="abandon" id="abandon" type="radio" value="non" <?php echo $abandon_non ?>> non </tr>
	 <tr>
        <td rowspan="2">Facture</td>
		<td>demandée le </td>
    	<td><input name="date_demande_facture" type="text" id="date_demande_facture"  style="TEXT-ALIGN:Center" size="10" maxlength="10"></td>
		<td>transmise pour paiement le</td>
    	<td><input name="date_transmission_facture" type="text" id="date_transmission_facture" size="10" maxlength="10"></td>
	 </tr>
	 <tr>
    	<td>reçue le </td>
   		<td><input name="date_reception_facture" type="text" id="date_reception_facture"  style="TEXT-ALIGN:Center" size="10" maxlength="10"></td>
		<td> payée le </td>
    	<td><input name="date_acquittee_facture" type="text" id="date_acquittee_facture"  style="TEXT-ALIGN:Center" size="10" maxlength="10"></td>
  	</tr>
    </table>
	<p>&nbsp;</p>

    <h2>VALORISATION</h2>
    <table cellpadding="4" cellspacing="1" class="texteP">
      <tr> 
        <td>Libellé :</td>
        <td><textarea name="valorisation_comment" cols="80%" rows="1" id="valorisation_comment"><?php echo $valorisation_comment ?></textarea></td>
      </tr>
      <tr> 
        <td>Lien WEB&nbsp;:</td>
        <td><input name="valorisation_url" type="text" id="valorisation_url" value="<?php echo $valorisation_url ?>" size="80%"></td>
      </tr>
    </table>
    <p>&nbsp;</p>

    <table cellpadding="10" cellspacing="0">
      <tr >
        <td><input name="Envoyer" type="submit" id="Envoyer" value="Mettre à jour" disabled></td>
		<td>&nbsp;</td>
        <td><input name="precedent" type="button" id="precedent" value="Précédent" onclick="javascript:parent.leftFrame.gotopage(<?php echo $prec ?>);"></td>
		<td><input name="suivant" type="button" id="suivant" value="Suivant" onclick="javascript:parent.leftFrame.gotopage(<?php echo $suiv ?>);"></td>
		<td>&nbsp;</td>
      </tr>
    </table>
    <input name="prec" type="hidden" id="prec" value="<?php echo $prec ?>">
    <input name="suiv" type="hidden" id="suiv" value="<?php echo $suiv ?>">
  </form>
</div>
<script language="JavaScript">
	active_envoi();
</script>
</body>
</html>
