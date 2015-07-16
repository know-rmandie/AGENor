<?php 
require_once("param_server.php"); 
// mise à jour septembre 2013
$sql='';
$Tservice='';
$Tuser='';
$Tpilote='pasdedroitdeconnexion';
$Tmdp='';
$Tcode='';
$ok=0;
$id_etude=1;
$requete1='';

if (isset($_GET['Tmdp'])) {$Tmdp = $_GET['Tmdp'];};
if (isset($_GET['Tcode'])) {$Tcode = $_GET['Tcode'];};

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
		$Tservice=$row['service'];
		$Tpilote=str_replace(" ","",$row['pilotage_ddtm']);
		$Tuser=$row['username'];
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
		$ok=1;
		};
if ($ok==1) {
		$nomtable='suivi_etudes.liste_etudes';
		$requete1  = 'insert into ' . $nomtable . ' (service, pilotage_ddtm,annee_pgm) VALUES (\''. $Tservice.'\',\''. $Tpilote.'\',\''.date("Y").'\') returning id_etude_hn';
		try
			{	
			$result = $connexion->prepare($requete1) ;
			$result->execute();
			/*$requete2  = 'select id_etude_hn from ' . $nomtable . ' order by id_etude_hn desc' ;
			$result = $connexion->prepare($requete2) ;
			$result->execute();
			*/
			$row=$result->fetch(PDO::FETCH_ASSOC);
			$id_etude = $row['id_etude_hn'];
			} 
	  	catch (PDOException $e) {
		print $e->getMessage();
		};
	}
$connexion=NULL;
?>
			
<html>
<head>
<title>Document sans titre</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

</head>
<body bgcolor="#FFFFFF">
<form name="form1" method="post" action="suivi_etudes_hn_liste.php" target="leftFrame">
  <input type="text" name="service" value="<?php echo $Tservice ?>">
  <input type="text" name="pilotage_ddtm" value="<?php echo $Tpilote ?>">
  <input type="text" name="annee_pgm" value="<?php echo date("Y") ?>">
  <input type="text" name="service" value="<?php echo $Tservice ?>">
</form>
<script language="JavaScript">
	//parent.leftFrame.location.reload(true);
	parent.leftFrame.location.href='suivi_etudes_hn_liste.php?new_fiche=<?php echo $id_etude; ?>';
	//parent.leftFrame.gotopage(<?php echo $id_etude; ?>);
</script>

</body>
</html>