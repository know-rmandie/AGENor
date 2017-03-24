var id_etude_before ;

function active_envoi() {
	droit_envoi=$("#T_autorisation").val();
	chaine_reference=$("#pilotage_ddtm").val();
	chaine_service=$("#service").val();
	//chaine_bop=$("#bop").val();
	chaine_bop=$("#libelle_ligne_budgetaire").val();
	ensemble_droit = droit_envoi.split(',');
	//document.getElementById('EnregistrerNlleFiche').disabled=true;
	
	document.getElementById('Envoyer').disabled=true;
	active_select(true);
	document.getElementById("Envoyer").title="Authentifiez vous pour faire vos mises à jour";
	
	for (var i=0; i<ensemble_droit.length;i++) {
		if ((droit_envoi!="") && ((droit_envoi.indexOf("tout") >-1) ||(chaine_reference.indexOf(ensemble_droit[i]) >-1) ||(chaine_service.indexOf(ensemble_droit[i]) >-1)|| (chaine_bop.indexOf(ensemble_droit[i]) >-1))){
			document.getElementById('Envoyer').disabled=false;
			active_select(false);
			document.getElementById("Envoyer").title="Cliquez pour enregistrer vos modifications";
			return; 
		}
	};
	maj_date_fiche();
};

function active_select(activer){
	document.getElementById('sel_theme_princ').disabled=activer;
	document.getElementById('sel_theme_second').disabled=activer;
	document.getElementById('service').disabled=activer;
	document.getElementById('bop').disabled=activer;
	document.getElementById('ligne_budgetaire').disabled=activer;
}

function init_autorisation(){
	$("#mdp").val('');
	$("#login").val('');
	get_autorisation();
}

function get_autorisation(){
	$("#message_auth").empty();
	$("#message_auth").append("Bonjour;</br></br>Entrez votre <b>LOGIN</b> et <b>mot de passe</b> pour mettre à jour les fiches. </br>Si vous ne les avez pas, contactez un administrateur");
	document.getElementById('NlleFiche').disabled=true;
	if (($("#login").val()!='')&& ($("#mdp").val()!='')){
		$.get("affiche_droit.php",{login:$("#login").val(),mdp:$("#mdp").val()},function(data){	
			mon_droit=data.split("[!]");
			noms_champs=mon_droit[0].split("|");
			valeurs_champs=mon_droit[1].split("|");
			for (i=0;i<noms_champs.length;i++) {
				$("#"+noms_champs[i]).val(valeurs_champs[i]);
			}
			$("#message_auth").empty();
			if ($("#T_utilisateur").val()!="") {
				$("#message_auth").append("</br></br>Bonjour "+ $("#T_utilisateur").val());			
				document.getElementById('NlleFiche').disabled=false;
				} else {
				$("#message_auth").append("</br></br>Utilisateur avec le LOGIN "+ $("#login").val() + " non trouvé avec ce mot de passe. </br>Vérifiez vos identifiants svp ou contactez un administrateur");
				document.getElementById('NlleFiche').disabled=true;
				}
			active_envoi();
		})
	}
	active_envoi();
}

function set_maj(){
	var Madate=new Date();
	Madate_maj = Madate.getFullYear()+"-"+(Madate.getMonth()+1)+"-"+Madate.getDate();
	$("#date_maj").val(Madate_maj);
	document.getElementById("exec_maj").submit();
	var ligne=$("#"+$('#id_etude').val());
	displayVals();
	//maj_date_fiche();
}

function maj_date_fiche() {
	if ($("#date_maj").val()!="") {
		Madate_fiche = $("#date_maj").val().split(" ");
		Madate_fiche = Madate_fiche[0].split("-");
		$("#date_fiche").val(Madate_fiche[2] + '-' + Madate_fiche[1] + '-' + Madate_fiche[0]);
	} else {
		$("#date_fiche").val("");
		}
}

function ajouter_fiche(){
	var tab_liste_champs_etude=liste_champs_etude.split(",");
	var Madate=new Date();
	Madate_maj = Madate.getFullYear()+"-"+(Madate.getMonth()+1)+"-"+Madate.getDate();
	id_etude_before = $("#id_etude").val();
	$("#id_etude").val("");
	for (i=0;i<tab_liste_champs_etude.length;i++) {
		$("#"+tab_liste_champs_etude[i]).val("");
	}
	$("#theme_princ").empty();
	$("#theme_second").empty();
	$("#maligneaction").hide();
	
	$("#date_maj").val(Madate_maj);
	$("#annee_pgm").val(Madate.getFullYear());
	$("#service").val($("#T_service").val());
	remplit_bop();
	$("#maligneaction").hide();
	
	$("#ligne_budgetaire_lb").empty();
	$("#ligne_budgetaire_lb").val($("#bop").val());
	$("#priorite").val(3);
	$("#abandon").val(0);
					
	//document.getElementById('Envoyer').disabled=true;
	//document.getElementById('EnregistrerNlleFiche').disabled=false;
	$("#Envoyer").val("Sauvegarder");
	//$("#NlleFiche").val("Effacer tout");
	$("#NlleFiche").val("Abandonner");
	document.getElementById('Envoyer').disabled=false;
	active_select(false);
}

function maj_ajout_fiche(){
	if ($("#Envoyer").val()=="Mettre à jour") {
		set_maj() ;
		//alert("set_maj");
		}
	if ($("#Envoyer").val()=="Sauvegarder") {
		if($("#themes").val()!='') {
			sauvegarder_nlle_fiche() ;
			} else {
			alert("Vous devez choisir un thème");
			}
		}
}

function ajout_abandon_fiche(){
	if ($("#NlleFiche").val()=="Ajouter une étude") {
		ajouter_fiche() ;
		//alert("ajouter_fiche");
		} else {
			if ($("#NlleFiche").val()=="Abandonner") {
			raz_fiche() ;
			//alert("raz_fiche");
			}
		}
}
function raz_fiche(){
	var container = $('#tBodyContainer') ;
	$("#NlleFiche").val("Ajouter une étude");
	$("#Envoyer").val("Mettre à jour");
	$('#id_etude').val(id_etude_before);
	affiche_detail('#'+id_etude_before);
	//displayVals();
	container.animate({scrollTop: $('#id_etude').offset().top - container.offset().top + container.scrollTop()}, 20,'');
	active_envoi();
}

function sauvegarder_nlle_fiche() {
// insère la nouvelle fiche dans la table
    var new_fiche={};
	var container = $('#tBodyContainer') ;
	var	tab_liste_champs_etude=liste_champs_etude.split(",");
	for (i=0;i<tab_liste_champs_etude.length;i++) {
		new_fiche[tab_liste_champs_etude[i]]=$("#"+tab_liste_champs_etude[i]).val();
	}

	$.get("exec_ajouter_fiche.php",new_fiche,function(data){
		$('#id_etude').val(data);
		displayVals();
		container.animate({scrollTop: $('#id_etude').offset().top - container.offset().top + container.scrollTop()}, 20,'');
		$("#NlleFiche").val("Ajouter une étude");
		$("#Envoyer").val("Mettre à jour");
		//active_envoi();
	});
}