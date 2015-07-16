<?php 
require_once("param_server.php"); 
// version septembre 2013
$sql='';
$Tservice='';
$Tuser='';
$Tpilote='pasdedroitdeconnexion';
$Tmdp='';
$Tcode='';

if (isset($_POST['Tmdp'])) {$Tmdp = $_POST['Tmdp'];};
if (isset($_POST['Tcode'])) {$Tcode = $_POST['Tcode'];};
		

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
$display='';
$nomtable='suivi_etudes.droits_maj'; 
$requete1  = 'select * from ' . $nomtable . ' WHERE  (code_suivi=\''.$Tcode. '\') and (mdp=\''.$Tmdp.'\')' ;
try
	{	
	$result = $connexion->prepare($requete1) ;
	$result->execute();
	while ($row=$result->fetch(PDO::FETCH_ASSOC)) {
		$Tuser=$row['username'];
		$Tservice=$row['service'];
		$Tpilote=str_replace(" ","",$row['pilotage_ddtm']);
		} ;
	if ($Tpilote=='tout'){$Tpilote='';};
	}
  	catch (PDOException $e) {
    print $e->getMessage();
  	};
	if (empty($Tuser) or ($Tuser=='')) {
		$display='disabled=true';
		}
		else
		{
		$display='disabled=false';
		};
		//Style["display"] = "none";
		//Style["display"] = "inline";
		//document.getElementById("Button1").disabled=true
//------------- Suppression des connexions au serveur POSTGRESQL
$id_init = $_POST['id_init'] ;
$connexion=NULL;
?>				
<html>
<script language="JavaScript" type="text/JavaScript">
<!--
	parent.topFrame.document.getElementById("Tmdp").value='<?php echo $Tmdp ?>';
	parent.topFrame.document.getElementById("Tcode").value='<?php echo $Tcode ?>';
	parent.topFrame.document.getElementById("Tuser").value='<?php echo $Tuser ?>';
	parent.topFrame.document.getElementById("Tservice").value='<?php echo $Tservice ?>';
	parent.topFrame.document.getElementById("Tpilote").value='<?php echo $Tpilote ?>';
	parent.topFrame.document.getElementById("button_new").<?php echo $display ?>;
//-->
// 
parent.leftFrame.gotopage(<?php echo $id_init ; ?>);
</script>
<head>
<title>Document sans titre</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
</body>
</html>
