<!DOCTYPE html>
<?php
	require_once("connexion.php");
	$etudes=new MesEtudes;
?>
<html lang="fr">
<head>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
		<meta http-equiv="Expires" content="0" />

		<link rel="stylesheet" href="lib/normalize-min.css" />
		<link rel="stylesheet" href="lib/font-awesome.min.css" />
		<link rel="stylesheet" href="style/main.css" />
        <link rel="apple-touch-icon" sizes="57x57" href="images/favicons/apple-icon-57x57.png"><link rel="apple-touch-icon" sizes="60x60" href="images/favicons/apple-icon-60x60.png"><link rel="apple-touch-icon" sizes="72x72" href="images/favicons/apple-icon-72x72.png"><link rel="apple-touch-icon" sizes="76x76" href="images/favicons/apple-icon-76x76.png"><link rel="apple-touch-icon" sizes="114x114" href="images/favicons/apple-icon-114x114.png"><link rel="apple-touch-icon" sizes="120x120" href="images/favicons/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="images/favicons/apple-icon-144x144.png"><link rel="apple-touch-icon" sizes="152x152" href="images/favicons/apple-icon-152x152.png"><link rel="apple-touch-icon" sizes="180x180" href="images/favicons/apple-icon-180x180.png"><link rel="icon" type="image/png" sizes="192x192"  href="images/favicons/android-icon-192x192.png"><link rel="icon" type="image/png" sizes="32x32" href="images/favicons/favicon-32x32.png"><link rel="icon" type="image/png" sizes="96x96" href="images/favicons/favicon-96x96.png"><link rel="icon" type="image/png" sizes="16x16" href="images/favicons/favicon-16x16.png"><link rel="manifest" href="images/favicons/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff"><meta name="msapplication-TileImage" content="images/favicons/ms-icon-144x144.png"><meta name="theme-color" content="orange">
		<script src="lib/jquery-1.12.0.min.js" charset="utf-8"></script>
		<script src="lib/pgi.js" charset="utf-8"></script>
		<script src="user/pgi_config.js" charset="utf-8"></script>
		<script src="js/initialisation.js" charset="utf-8"></script>
		<script src="js/fonct_onglets.js" charset="utf-8"></script>
        <script src="js/detail.js" charset="utf-8"></script>
        <script src="js/maj.js" charset="utf-8"></script>
		  <script src="js/print.js"></script>
		<script>
			var annee_0 = "<?php echo $etudes->_annee_0; ?>" ;
			var liste_champs_etude="<?php echo $etudes->_liste_champs_etude ?>";

			<?php
				$ma_liste_champs_excel='general,financement,devis,compta,avanc_devis,avanc_etude,avanc_facturation,valorisation';
				$mes_libelle_champs_excel='Généralité,Financement,Devis,Suivi compta,Avancement devis,Avancement étude,Avancement facturation,Valorisation';
				echo 'var ma_liste_champs_excel ="'. $ma_liste_champs_excel.'";'."\n";
				$tab_liste_libelles_excel = explode(",",$mes_libelle_champs_excel);
				$tab_liste_champs_excel = explode(",",$ma_liste_champs_excel);
				for ($i=0;$i<count($tab_liste_champs_excel);$i++) {
					echo "mes_champs_excel['".$tab_liste_champs_excel[$i] ."']= '" . $etudes->get_value("champs_". $tab_liste_champs_excel[$i]) ."';\n" ;
					echo "mes_libelles_excel['". $tab_liste_champs_excel[$i] ."']= '" . $tab_liste_libelles_excel[$i] ."';\n" ;
				}

				echo 'liste_budgets='.$etudes->get_bop() .";\n" ;
				echo 'liste_themes='.$etudes->get_thematiques() .";\n" ;
				echo "liste_structures=".$etudes->get_structures() .";\n" ;
			?>

			$(document).ready(function(){
				onglet_01(anc_onglet_01) ;
				onglet_02(anc_onglet_02) ;
				onglet_03(anc_onglet_03) ;

				// ---------   Menu déroulant années -----
				// par défaut c'est l'année en cours qui est sélectionnée
				var aujourdhui=new Date() ;
				var annee = aujourdhui.getFullYear();
				var date_jour =aujourdhui.getDate()+"/"+(aujourdhui.getMonth()+1)+"/"+aujourdhui.getFullYear() ;
				var select_annee;
				select_annee ='<option value="">Années</option>'+"\n";
				for (i=annee_0;i<=annee+1;i++) {
					select_annee +='<option value="'+i+'">'+i+'</option>'+"\n";
				}
				select_annee += '<option value="">Toutes</option>';
				select_annee = select_annee.replace('<option value="'+ annee+'">', '<option value="'+ annee +'" selected>');
				$('#selection_annee').html(select_annee);

				/* affichage menu déroulant BOP
				liste_select = "action_bop as valeur,action_bop";
				nom_table = "bop";
				condition = "WHERE 1=1";
				mondiv = "#maliste_bop";
				affiche_select(liste_select,nom_table,condition,mondiv) ;
				*/

				//affichage menu déroulant Thématique en haut de page
				$("#selection_thematiques").empty();
				$("#selection_thematiques").append('<option selected value="">Tous les thèmes</option>');
				$("#Excel_thematiques").empty();
				$("#Excel_thematiques").append('<option selected value="">Tous les thèmes</option>');
				for(var key in liste_themes) {
					$("#selection_thematiques").append('<option value="'+key+'">'+ liste_themes[key].substring(0, 25)+'</option>');
					$("#Excel_thematiques").append('<option value="'+key+'">'+ liste_themes[key].substring(0, 25)+'</option>');
				}

				// menu déroulant gestionnaires
				affiche_gestionnaire("#maliste_gestionnaire","");
				//affiche_gestionnaire("#id_gestion","dreal");
				$("#select_annee").change(displayVals);
				$("#selection_thematiques").change(displayVals);
				$("#select_gestionnaire").change(displayVals);
				$("#select_avanc").change(displayVals);
				displayVals();

				get_autorisation();
				remplit_bop();
				remplit_sel_theme_princ();
				remplit_structures();

			});

			function affiche_table(liste_select,larg_table, nom_table,condition,mondiv) { // mondiv est de la forme "#montableau"
				ref_tableau.liste_select = liste_select;
				ref_tableau.larg_table = larg_table;
				ref_tableau.nom_table = nom_table;
				ref_tableau.condition = condition;

				mes_colonnes=[];
				mes_colonnes = liste_select.split(",");
				ma_liste_colonne = [];
				for (i=0;i<mes_colonnes.length; i++) {
					ma_liste_colonne.push({label:ma_liste_colonne[i]});
				}

				$.get("affiche_table.php",ref_tableau,function(data){
						$(mondiv).empty();
						$(mondiv).append(data);
				});
			} ;

			function affiche_select(liste_select,nom_table,condition,mondiv) { // mondiv est de la forme "#montableau"
				ref_tableau.liste_select = liste_select;
				ref_tableau.nom_table = nom_table;
				ref_tableau.condition = condition;
				mes_colonnes=[];
				mes_colonnes = liste_select.split(",");
				ma_liste_colonne = [];
				for (i=0;i<mes_colonnes.length; i++) {
					ma_liste_colonne.push({label:ma_liste_colonne[i]});
				}
				$.get("affiche_select.php",ref_tableau,function(data){
						$(mondiv).empty();
						$(mondiv).append(data);
				});
			} ;

			function affiche_gestionnaire(mondiv,mon_id) { // mondiv est de la forme "#montableau"
				$.get("affiche_gestionnaires.php?mon_id="+mon_id,function(data){
						$(mondiv).empty();
						$(mondiv).append(data);
				});
			} ;

			function affiche_dotations(v_annee, id_div){
				liste_select = "id_dotation, sigle_bop_dotation as sigle,montant_dotation as montant";
				larg_table="20%,40%,40%";
				nom_table = "dotation";
				condition = (v_annee=='')?"": "WHERE annee_dotation ='"+v_annee+"'";
				affiche_table(liste_select,larg_table,nom_table,condition,id_div)
			}

			function affiche_etudes(v_annee,v_dreal,v_bop,v_service,v_pilote,v_titre,v_avanc, v_themes){
				// affichage Etudes année N
				// ($annee,$dreal,$bop,$pilote,$titre,$avanc)
				ref_tableau = {};
				ref_tableau.annee = v_annee;
				ref_tableau.dreal = v_dreal;
				ref_tableau.bop = v_bop;
				ref_tableau.service = v_service;
				ref_tableau.pilote = v_pilote;
				ref_tableau.titre = v_titre;
				ref_tableau.avanc = v_avanc;
				ref_tableau.themes = v_themes;

				$.get("affiche_table_etudes.php",ref_tableau,function(data){
						$("#tHeadContainer").empty();
						// $("#tHeadContainer").append(data.replace('<tr id="','<tr id="tH'));
						var tHeadContainer_tab=data.replace(/id="l/gi,'id= "l')
						$("#tHeadContainer").append(tHeadContainer_tab.replace(/id="/gi,'id="tH'));
						$("#tBodyContainer").empty();

						$("#tBodyContainer").append(data.replace("cl_entete_tab_etudes","cl_entete_tab_etudes_bis"));
						var y = $('#id_etude').val();
						//var mondata = data;
						if (( y =='') || (data.indexOf('id="'+y+'"')<0)){
							var id00,id01,id02,id03,id04;
							id00=data.indexOf("<tr")+30; // ligne entête
							id01=data.indexOf("<tr",id00)+30; // 1ère ligne tbody
							id02=data.indexOf("<tr",id01)+30; // 1ère ligne avec id étude
							id03=data.indexOf("id=",id02);
							id04=data.indexOf('"',id03+4);
							y=data.substring(id03+4, id04);
							affiche_detail("#"+y);
						}
						test(y);
				});
			} ;

			function test(y) {
				var container = $('#tBodyContainer') ;
				var ligne=$("#"+y);
				x = $("#"+y).position();
				container.animate({scrollTop: x.top - container.offset().top +container.scrollTop()- 200}, 20,'');
				selected_ligne(ligne);
				}


			function displayVals() {
				//var nom_bop = $("#select_bop").val() ? $("#select_bop").val():''; // valeur dans le select BOP
				var nom_bop =""; //menu déroulant BOP supprimé
				var nom_annee = $("#selection_annee").val(); // valeur dans le select année
				var nom_gestionnaire_dreal =$("#select_gestionnaire").val(); // valeur dans le select Gestionnaire
				var nom_service =$("#select_service").val(); // nom du pilote;
				var nom_pilote =$("#select_pilote").val(); // nom du pilote;
				var nom_titre =$("#select_titre").val(); // un mot dans le titre;
				var nom_avanc =$("#select_avanc").val();
				var nom_themes =$("#selection_thematiques").val();
				remplir_select_excel();
				$("#monchoix").html('<a>Liste des études ' + nom_bop + ' ' + nom_annee+'</a>');
				$("#madotation").html('<a>Dotation ' + nom_annee+'</a>');
				affiche_etudes(nom_annee,nom_gestionnaire_dreal,nom_bop,nom_service,nom_pilote,nom_titre,nom_avanc,nom_themes) ;
				affiche_dotations(nom_annee, "#panneau-madotation");
			}

			function remplir_select_excel(){
				$("#Excel_dreal").val($("#select_gestionnaire").val());
				$("#Excel_service").val($("#select_service").val());
				//$("#Excel_bop").val($("#select_bop").val());
				$("#Excel_pilotage_ddtm").val($("#select_pilote").val());
				$("#Excel_titre").val($("#select_titre").val());
				$("#Excel_annee").val($("#selection_annee").val());
				$("#Excel_avanc").val($("#select_avanc").val());
				$("#Excel_thematiques").val($("#selection_thematiques").val());

			}

			function ouvre_fenetre(lien) {
				w = window.open(lien,"fiche","menubar=no, status=no, scrollbars=yes, width=1000, height=800");
				w.focus();
			}

			function exec_action_bop(o) {
					$("#ligne_budgetaire_lb").empty();
					$("#ligne_budgetaire_lb").val($("#bop").val());
					if($("#bop").val()!='') {
						$("#dreal").val(liste_budgets[$("#bop").val()][1]);
						}
					remplit_action_bop(o);
			}

			function remplit_bop(){
				 var selectBop = $('select[name="bop"]');
				 selectBop.empty();
				 selectBop.append('<option selected value="">Liste des BOP</option>');
				 for(var key in liste_budgets) {
					montext=key  +' - ' + liste_budgets[key][0];
					montext = (montext.length>30)?montext.substring(0, 30)+'..':montext;
					selectBop.append('<option value="'+key+'">'+ montext+'</option>');
					}
				 selectBop.append('<option value="autre">Autre</option>');
				 selectBop.append('<option value="pas de financement">Pas de financement</option>');
			}

			function remplit_action_bop(o){
				 var selectBopAction = $('select[name="ligne_budgetaire"]');
				 var id_bop=$("#bop").val();
				 var liste_budgets_action;

				 selectBopAction.empty();
				 selectBopAction.append('<option selected value="">Ligne Budgétaire</option>');
				 $("#maligneaction").hide();
				 if ((id_bop !==null) && (id_bop !=='')){
					$("#libelle_ligne_budgetaire").empty();
					$("#libelle_ligne_budgetaire").text("BOP : " + liste_budgets[id_bop][0] );
					if (liste_budgets[id_bop][2] !==null) {
						liste_budgets_action=liste_budgets[id_bop][2];
						for(var key in liste_budgets_action) {
							if ($("#ligne_budgetaire_lb").val()==key) {
								selectBopAction.append('<option value="'+key+'" selected>'+ (key  +' - ' + liste_budgets_action[key][0]).substring(0, 30)+'..</option>');
								$("#libelle_ligne_budgetaire").text("BOP : " + liste_budgets_action[key][0] );
								} else
								selectBopAction.append('<option value="'+key+'">'+ (key  +' - ' + liste_budgets_action[key][0]).substring(0, 30)+'..</option>');
								}
							}
						$("#maligneaction").show();
						}
					}

			function remplit_ligne_budgetaire(o){
				$("#ligne_budgetaire_lb").empty();
				$("#ligne_budgetaire_lb").val($("#"+o.id).val());
				affiche_libelle_ligne_budgetaire(o);
			}

			function affiche_libelle_ligne_budgetaire(o) {
				var bop_encours;
				var id_bop=$("#bop").val();
				$("#libelle_ligne_budgetaire").empty();
				bop_encours = $("#ligne_budgetaire_lb").val();
				if ($("#ligne_budgetaire option[value='+bop_encours+']").length <= 0) {
					if((id_bop !==null) && (id_bop !=='')) {
						/*
						var malignebudget=$("#ligne_budgetaire_lb").val();
						$("#ligne_budgetaire").val(malignebudget);
						$("#ligne_budgetaire").val(malignebudget);
						var liste_budgets_action=liste_budgets[id_bop][2];
						bop_encours=($("#ligne_budgetaire").val()=="")?liste_budgets[id_bop][0]:liste_budgets_action[$("#ligne_budgetaire").val()][0];
						*/
						bop_encours=liste_budgets[id_bop][0] ;
						} else {
						$("#ligne_budgetaire").val(bop_encours);
						bop_encours= $(o).find('td').eq(1).html();
						if ((bop_encours !==null) && (bop_encours !=='') && (bop_encours !== undefined)) {
							bop_encours= bop_encours.substring(bop_encours.indexOf("- ")+2);
							}
						}
				}
				$("#libelle_ligne_budgetaire").text("BOP : " + bop_encours );
			}

			function filtre_avanct(arg) {
				$('#select_avanc').val(arg);
				displayVals();
			}
		</script>
		<title>AGENor</title>
	</head>

<body onresize="displayVals();">
<div class="container">
	<header class="cadre-bandeau-haut">
		<div class="myselect-01"><span title="Application de Gestion des Etudes Normandes">AGENor</span></div>
		<div id="maliste_annee" class="myselect-02">
			<select name="selection_annee" size="1" id="selection_annee" onchange="javascript:displayVals();"></select>
		</div>
		<div id="maliste_thematique" class="myselect-03">
			Thématiques : <select name="selection_thematiques" size="1" id="selection_thematiques" onchange="javascript:displayVals();" title="non actif"></select>
		</div>
		<div id="maliste_gestionnaire" class="myselect-04"></div>
		<div id="maliste_service" class="myselect-05">Direction : <input id="select_service" onchange="javascript:displayVals();" title="Nom de la Structure"/></div>
		<div id="maliste_pilote" class="myselect-05">Pilote : <input id="select_pilote" onchange="javascript:displayVals();"/></div>
		<div id="maliste_titre" class="myselect-06">Titre : <input id="select_titre" onchange="javascript:displayVals();" title="un mot dans le titre de l'étude"/></div>
		<div id="maliste_avanc" class="myselect-07">
			<select name="select_avanc" size="1" id="select_avanc" onchange="javascript:displayVals();">
				<option value="">Avancement</option>
				<option value="idee">Idée</option>
				<option value="projet">Projet</option>
				<option value="programmee">Programmée</option>
				<option value="en_cours">En cours</option>
				<option value="terminee">Terminée</option>
				<option value="valorisee">Valorisée</option>
				<option value="abandonnee">Abandonnée</option>
			</select>
		</div>
	</header>
	<section class="cadre-centre">
		<section class="cadre-interne-gauche" id="cadre-interne-gauche">
			<nav>
					<ul>
						<li id="monchoix" onclick="javascript:onglet_01(this)"><a>Liste des études 2014</a></li>
						<!--li id="madotation" onclick="javascript:onglet_01(this)"><a>Dotation 2014</a></li-->
						<li id="Mode_emploi" onclick="javascript:onglet_01(this)"><a>Mode d'emploi</a></li>
						<li id="liens_utiles" onclick="javascript:onglet_01(this)"><a>Liens utiles</a></li>
						<li id="exporter_excel" onclick="javascript:onglet_01(this)"><a>Exporter</a></li>
						<li style="float:right" id="circe" onclick="javascript:ouvre_fenetre('http://www.etudes-normandie.fr/')" Title="Le Catalogue Interactif Régional de Consultation des Etudes"><a>Circé</a></li>
					</ul>
			</nav>
			<div>
					<div class="panneau panneau-masque" id="panneau-monchoix" style="overflow: hide;">
					<!-- études de l'année sélectionnée-->
						<div id="scrollTableContainer">
							<div id="tHeadContainer"> </div><!-- tHeadContainer -->
							<div id="tBodyContainer"></div><!-- tBodyContainer -->
						</div><!-- scrollTableContainer -->
					</div>
					<!--div class="panneau panneau-masque" id="panneau-madotation" style="overflow: hide;"><p>Dotation de l'année sélectionnée</p></div-->
					<div class="panneau panneau-masque" id="panneau-Mode_emploi">
					<iframe id="doc_agenor" src="http://know-rmandie.gitlab.io/AGENor/" name="doc_agenor"></iframe>
					</div>
					<div class="panneau panneau-masque" id="panneau-liens_utiles"><?php include("includes/liens_utiles.html"); ?></div>
					<div class="panneau panneau-masque" id="panneau-exporter_excel"><?php include("includes/export_excel.html"); ?></div>
			</div>
		</section>
		<section class="cadre-interne-droit">

			<!--div id="menus"-->
			<nav>
					<ul>
						<li id="myEtude" onclick="javascript:onglet_02(this)"><a>fiche</a></li>
						<!--<li id="carto" onclick="javascript:onglet_02(this)"><a>localiser</a></li>-->
						<li id="authentification" onclick="javascript:onglet_02(this)"><a>S'authentifier</a></li>
						<li id="impression_fiche" onclick="printable('fiche')"><i class="fa fa-print fa-2x"></i></li>
						<li id="contact_comite" onclick="javascript:ouvre_fenetre('http://intra.dreal-normandie.e2.rie.gouv.fr/etudes-et-publications-r2568.html')"><a>Actualité des études</a></li>
					</ul>
			</nav>
			<div>
					<div class="panneau panneau-masque" id="panneau-myEtude">
						<span class="fin_impression_fiche" onclick="printable('fiche')"><i class="fa fa-times fa-2x"></i></span>
						<?php include("includes/detail.html"); ?></div>
					<div class="panneau panneau-masque" id="panneau-carto">Cartographie - localisation des études</div>
					<div class="panneau panneau-masque" id="panneau-authentification">
						<span class="fin_impression_fiche" onclick="printable('fiche')"><i class="fa fa-times fa-2x"></i></span>
						<?php include("includes/identification.html"); ?></div>
					<div class="panneau panneau-masque" id="panneau-contact_comite">A définir (lien vers intranet ?)</div>
			</div>
			<!--/div-->
		</section>
	</section>
	<footer class="cadre-bandeau-bas" id="cadre-bandeau-bas">
		<div id="legende">
			<span onclick="filtre_avanct('idee')" Title="Etude sans demande de financement" ><i class="fa fa-lightbulb-o"></i>idée ></span>
			<span onclick="filtre_avanct('projet')"  Title="Etude avec demande de financement mais pas d'autorisation" ><i class="fa fa-tasks"></i>projet ></span>
			<span onclick="filtre_avanct('programmee')" Title="Etude avec un financement Autorisé" ><i class="fa fa-eur"></i>programmée ></span>
			<span onclick="filtre_avanct('en_cours')" Title="Etude avec financement Engagé" ><i class="fa fa-arrow-right"></i>en cours ></span>
			<span onclick="filtre_avanct('terminee')" Title="Etude avec pourcentage d'avancement à 100%" ><i class="fa fa-check"></i>terminée ></span>
			<span onclick="filtre_avanct('valorisee')" Title="Etude avec l'un des champs de l'onglet 'Valoriser' renseigné" ><i class="fa fa-book"></i>valorisée |</span>
			<span onclick="filtre_avanct('abandonnee')"><i class="fa fa-times"></i>abandonnée |</span>
			<span onclick="filtre_avanct('')"><i class="fa fa-hand-paper-o"></i>toute</span>
		</div>
		<div onclick="javascript:ouvre_fenetre('http://know-rmandie.gitlab.io/AGENor/')">AGENor - v1.3.1-20161202</div>
		<div onclick="javascript:ouvre_fenetre('http://intra.dreal-normandie.e2.rie.gouv.fr/IMG/pdf/membres_du_cotech_etudes.pdf')">Comité des études Normandie</div>
		<div id="bugReport" class="pgiButton" title="Faites part de vos remarques sur l'outil AGENor"><i class="fa fa-bug"></i></div>
		<div title="Ecrivez-nous au sujet du programme d'études"><a href="mailto:pascal.capitaine@developpement-durable.gouv.fr?subject=suivi%20des%20etudes"><i class="fa fa-envelope"></i></a></div>
	</footer>
</div>
</body>

</html>
