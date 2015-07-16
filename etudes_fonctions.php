<?php
// jour d'effacement des fichiers sur le serveur
$nbjourdepot=3*24*3600;

// Bases de données
function ouvrebd() {
	global $echanges;
	global $database_echanges ;
	$hostname_echanges = "localhost";
	$database_echanges = "lolf";
	$username_echanges = "root";
	$password_echanges = "";
	$echanges = mysql_pconnect($hostname_echanges, $username_echanges, $password_echanges) or die(mysql_error());
}

function execsql($query_in) {
	global $echanges;
	global $database_echanges ;
	if (isset($echanges)) {
			mysql_select_db($database_echanges, $echanges);
			$in = mysql_query($query_in, $echanges) or die(mysql_error());
			}
			else
			{
			ouvrebd();
			$in=execsql($query_in) ;
			}
	return $in ;
}

function majtable($table,$set,$cond) {
	$query_in="UPDATE ". $table . " SET ". $set . " WHERE ". $cond ;
	return execsql($query_in);
}	

function ajouttable($table, $champs,$valeurs) {
	$query_in="INSERT INTO ". $table . " ". $champs . " VALUES ". $valeurs ;
	return execsql($query_in);
}	

function supptable($table, $cond) {
	$query_in="DELETE FROM ". $table . " WHERE ". $cond ;
	return execsql($query_in);
}		
//-----------------------------------------------------------------
// transmission
function mailto($vers_nom,$vers_mail,$sujet,$contenu) {
	/*
	$vers_nom = Nom du receveur
	$vers_mail =Email du receveur
	$sujet = Sujet du mail
	$contenu = contenu du mail
	*/
	if($vers_mail ==""){$vers_mail = "fouad.gafsi@equipement.gouv.fr";}; 
	$style="font-family: 'Verdana, Arial, Helvetica, sans-serif' ; color='#003399'; font-size: 12px ";
	$message = "<div style=\"".$style."\"><p>Bonjour ". $vers_nom.",</p>"
				. $contenu
				. "<p>A+</p></div>";
	$message =str_replace("\'","'",$message);

	/** Envoi du mail **/
	$entete = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
	//$entete .= "To: $vers_nom <$vers_mail>\r\n";
	//$entete .= "From: $de_nom <$de_mail>\r\n";
	if(!mail($vers_mail, $sujet, $message, $entete)){
 		$rapport= "Attention : l'email n'a pas été envoyé à ".$vers_nom . " ! Contactez votre webmestre";
		} 
		else
		{
 		$rapport="NOTEZ BIEN VOTRE LOGIN %s. <br>Un e-mail a été envoyé à l'adresse ".$vers_mail . " avec votre code d'accès. <br>Quand vous l'aurez, rendez-vous à la page d'accueil et entrez vos paramètres.";
		}
	return $rapport ;
}

//-------------------------------------------------------------------
function couleurligne($initcouleur) {
	static $colortab ;
	// Couleur des lignes des tableaux
	// valeur de retour ancienne version : 
	//		$color01='bgcolor="#eeeeee"';
	//		$color02='bgcolor="#ffffff"';
	// nouvelle version : la valeur de retour est une classe composée
	// 		valeur en entrée : 0, idee, projet, programme, encours, terminee, valorisee
	// 		tr.lip {background-color:#fff;}
    // 		tr.lii {background-color:#eee;}
	// 		idée d'étude (=étude qui n'a pas de montant renseigné, ni de valorisation) > tr.idee
	// 		projet d'étude (=étude qui a un montant demandé non vide, et aucun autre) > tr.projet
	// 		étude programmée (=étude qui a un montant autorisé non vide et pas de montant engagé) > tr.programme
	// 		étude en cours (=étude qui a un montant engagé non vide) > tr.encours
	// 		étude terminée (=étude qui est à 100% mais pas valorisée) > tr.terminee
	// 		étude valorisée (=étude à 100% et valorisé est non vide) > tr.valorisee
	$color01='class="lip'; 
	$color02='class="lii';
	$couleur = '"'; 
	if ($initcouleur!='0') {
		 $couleur = ' '.$initcouleur .'"'; 
		 $colortab=($colortab==$color01) ? $color02 : $color01 ;
		 }
		 else 
		 {
		 $colortab=$color02;
		 } 
	return $colortab.$couleur;
}
	
function inversedate($d) {
	$resultat="";
	if (($d!="") and ($d!="0000-00-00") and ($d!="jj-mm-aaaa")) {
		$valtemp=split("-",$d);
		$resultat=$valtemp[2]."-".$valtemp[1]."-".$valtemp[0];
		}
	return $resultat;

}

function current_date() {
 // formate la date du jour au format aaaammjj
  $current_date=getdate(time());
  $dizainemois = ($current_date['mon']<10) ? "0" : "" ;
  $dizainejour = ($current_date['mday']<10) ? "0" : "" ;

  $date_seuil=$current_date['year']
  			  ."-"
			  .$dizainemois . $current_date['mon']
			  ."-"
			  .$dizainejour.$current_date['mday'];
	return $date_seuil ;
}
function couleurtabledate() {
	return "#CC99CC";
}
function couleurdate($d,$date_seuil,$title) {
	$resultat='class="Petittexte"';
	if (($d!="") and ($d!="0000-00-00")) {
	  	$resultat=($d<$date_seuil) ? 'bgcolor=' . couleurtabledate() .' style="font-size : 8px; color:'. couleurtabledate() .'" title="Terminé" valign="middle" ' : ' style="color:#003399; font-weight: bolder; font-size : 8px;" valign="middle" title="'.$title.'"';
		}
	return $resultat;
}


function fenetredetail($theURL) {
// crée une fenetre popup avec le modèle "detail_generateur.php"
// et y met la page $theURL qui doit aussi se trouver dans le répertoire details 
	$resultat="#";
	if ($theURL!="") {
		$theURL=str_replace('?','&',$theURL);
		$winName="Detail";
		$features="scrollbars=yes,resizable=yes,width=500,height=500";
		$resultat="window.open('details/detail_generateur.php?theURL=". $theURL."','".$winName."','".$features."');return false;";
	}
	return $resultat;
}

function detail_insitu($theURL) {
// ouvre dans la fenêtre popup une nouvelle page de detail
// avec le modèle "detail_generateur.php"
// et y met la page $theURL qui doit aussi se trouver dans le répertoire details 
	$resultat="#";
	if ($theURL!="") {
		$theURL=str_replace('?','&',$theURL);
		$resultat="window.location='detail_generateur.php?theURL=". $theURL."'";	
		}
	return $resultat;
}

function sql_in($query_in,$nom_id_req) {
/*
Mysql3 ne connait pas la fonction 'in'
	il faut donc remplacer par la focntion REGEXP
	module ajouter pour compatibilté avec mysql3 : 
	
Crée une condition analogue à [$nom_id_req in $query_in]
*/
	$In_selection='';
	$hdl=execsql($query_in);
	$row_in = mysql_fetch_assoc($hdl);
	$totalRows_in = mysql_num_rows($hdl);
	if ($totalRows_in !=0) {
		$liste_in='"';
		do {
			$liste_in= $liste_in . '_'. $row_in[$nom_id_req] .'_|';
			} while ($row_in = mysql_fetch_assoc($hdl));
		$liste_in=$liste_in.' "';
		$In_selection = " concat('_',".$nom_id_req.",'_') REGEXP ". $liste_in ;
	}
	return  $In_selection;
}

function sql_in_2($query_in,$nom_id_req1,$nom_id_req2,$database_bfc, $bfc) {
/*
Mysql3 ne connait pas la fonction 'in'
	il faut donc remplacer par la focntion REGEXP
	module ajouter pour compatibilté avec mysql3 : 
	
Crée une condition analogue à [($nom_id_req1,$nom_id_req2) in $query_in ]
*/
	$In_selection='';
	$hdl=execsql($query_in);
	$row_in = mysql_fetch_assoc($hdl);
	$totalRows_in = mysql_num_rows($hdl);
	if ($totalRows_in !=0) {
		$liste_in='"';
		do {
			$liste_in= $liste_in . '_'. $row_in[$nom_id_req1] .','. $row_in[$nom_id_req2] .'_|';
			} while ($row_in = mysql_fetch_assoc($hdl));
		$liste_in=$liste_in.' "';
		$In_selection = " concat('_',".$nom_id_req1."," .$nom_id_req2. ",'_') REGEXP ". $liste_in ;
	}
	return $In_selection ;
}
?>
