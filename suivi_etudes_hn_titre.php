<html>
<head>
<title>Suivi des études - Titre - version novembre 2013</title>
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
//-->

function deconnecter() {
	document.getElementById("Tcode").value="";
	document.getElementById("Tmdp").value="";
	document.getElementById("Tpilote").value="pasdedroitdeconnexion";
	document.getElementById("Tuser").value="";
	parent.mainFrame.location.reload();
}

function nlle_fiche() {
	parent.mainFrame.location.href='suivi_etudes_hn_create_fiche.php?Tcode='+document.getElementById("Tcode").value+'&Tmdp='+document.getElementById("Tmdp").value;
}
</script>
</head>

<body>
<form name="form1" method="post" action="identification.php" target="mainFrame">
  <H1>Suivi des Etudes HN<span class="texteP" align="right"  style="w ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
    code suivi&nbsp;: &nbsp; 
    <input name="Tcode" type="text" id="Tcode" size="10" maxlength="15">
    &nbsp;&nbsp;&nbsp;&nbsp; mdp&nbsp;:&nbsp; 
    <input name="Tmdp" type="password" id="Tmdp" size="10" maxlength="20">
    &nbsp;&nbsp;&nbsp;&nbsp; 
    <input type="submit" name="Submit" value="Envoyer">
    <input name="Tpilote" type="hidden" id="Tpilote" value="pasdedroitdeconnexion">
    <input name="Tuser" type="hidden" id="Tuser">
	<input name="Tservice" type="hidden" id="Tservice">
	<input name="id_init" type="hidden" id="id_init" value="">
    <input name="deconnecter" type="button" id="deconnecter" value="Déconnexion" onclick="javascript:parent.location.reload();">
	<input name="button_new" type="button" id="button_new" value="Créer une nouvelle fiche" onclick="javascript:nlle_fiche();" disabled>
    </span></H1>
</form>
