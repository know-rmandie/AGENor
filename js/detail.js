var o_ligne='';
var o_style='';
function insere_date(o){
	var aujourdhui=new Date() ;
	myid_date=$(o).attr('id').replace('montant','date');
	$("#"+myid_date).val(aujourdhui.getDate()+"-"+(aujourdhui.getMonth()+1)+"-"+aujourdhui.getFullYear());
}

function affiche_detail(o) {
	if ($("#Envoyer").val()=="Mettre à jour") {
        myid=$(o).attr('id');
		$("#libelle_ligne_budgetaire").empty();
		$("#theme_princ").empty();
		$("#theme_second").empty();
        // récupération des données études dans la base et affichage
		$.get("affiche_detail.php",{id_etude:myid},function(data){
			$("#id_etude").val(myid);
			mon_etude=data.split("[!]");
			noms_champs=mon_etude[0].split("|");
			valeurs_champs=mon_etude[1].split("|");
        // dispatch des valeurs dans le formulaire
			for (i=0;i<noms_champs.length;i++) {
                // cas des dates : nettoyage
				if (noms_champs[i].indexOf('date')>-1) {
					$("#"+noms_champs[i]).val(valeurs_champs[i].replace(' 00:00:00',''));
				} else {
					$("#"+noms_champs[i]).val(valeurs_champs[i]);
				}
                // récupération de l'élément. Doit avoir le nom de champs comme id
                x = document.getElementById(noms_champs[i]);
                console.log(x);
				txt='';
                // cas des boutons "radio" - ne peuvent utiliser un identifiant unique
                if(x!==null && x!==undefined){
                    if (x.getAttribute("type")=="radio") {
                        // remise des valeurs true / false pour les boutons radios que le script a fait sauter
                        // !todo, faire ça plus proprement en détectant les radios plus tot
                        var x_bis =  document.getElementById(noms_champs[i]+"_bis");
                        x.setAttribute("value","true"); x_bis.setAttribute("value","false");
                        if(valeurs_champs[i] == true) {
                            x.checked = true; //x_bis.checked = false;
                        }
                        else {
                            x_bis.checked = true; //x.checked = false;
                        }
                    }
                }
			}
			active_envoi();
			selected_ligne(o);
			remplit_action_bop(o);
			affiche_libelle_ligne_budgetaire(o);
			$("#sel_theme_second").empty();
			$("#sel_theme_second").append('<option selected value="">Thème(s) secondaire(s)</option>');
			init_themes();
		})
	}
}


function selected_ligne(o){
	if (o_ligne !="") {
		$(o_ligne).attr("style",o_style);
		}
	o_ligne = o;
	o_style=$(o).attr("style")?$(o).attr("style"):"";
	$(o).attr("style","background-color:#CCFFFF");
}

function remplit_theme_princ(o) {
	var mesThemes = $("#themes").val(); // contenu du champ INPUT lié à la base de données
	var monThemeprincipal =$("#sel_theme_princ option:selected").text();
	var monThemeprincipalID=$("#sel_theme_princ option:selected").val();
	$("#sel_theme_princ").val('');
	// renseigne le SPAN Libellé principal
	$("#theme_princ").empty();
	$("#theme_second").empty();
	$("#theme_princ").append(monThemeprincipal);
	// mise à jour de l'input
	mesThemes= mesThemes.replace(","+ monThemeprincipal,"");
	mesThemes= mesThemes.replace(monThemeprincipal+",","");
	mesThemes= mesThemes.replace(monThemeprincipal,"");
	$("#theme_second").append(mesThemes);
	mesThemes= (mesThemes=="")? monThemeprincipal : monThemeprincipal + ',' + mesThemes;
	$("#themes").val(mesThemes);
	remplit_sel_theme_second(monThemeprincipalID) ;
}

function remplit_theme_second(o) {
	var mesThemes = $("#theme_princ").html();
	var mesLibThemesSecondaires = $("#theme_second").html(); // libellé des champs secondaires dans le span
	var monThemeSecondaire =$("#sel_theme_second option:selected").text();
	$("#sel_theme_second").val('');
	var n=mesLibThemesSecondaires.indexOf(monThemeSecondaire);
	if (n>-1) {
		mesLibThemesSecondaires = mesLibThemesSecondaires.replace(","+ monThemeSecondaire,"");
		mesLibThemesSecondaires = mesLibThemesSecondaires.replace(monThemeSecondaire+",","");
		mesLibThemesSecondaires = mesLibThemesSecondaires.replace(monThemeSecondaire,"");
		} else {
		mesLibThemesSecondaires = (mesLibThemesSecondaires =="")? monThemeSecondaire : mesLibThemesSecondaires + ","+ monThemeSecondaire;
		}
	$("#theme_second").empty();
	$("#theme_second").append(mesLibThemesSecondaires);
	$("#themes").empty();
	mesThemes = (mesLibThemesSecondaires=="")? mesThemes : mesThemes + ',' + mesLibThemesSecondaires;
	$("#themes").val(mesThemes);
}

function init_themes(){
	$("#sel_theme_second").val('');
	$("#sel_theme_princ").val('');
	var mesThemes = $("#themes").val();
	$("#theme_princ").empty();
	$("#theme_second").empty();
	var n=mesThemes.indexOf(",");
	if (mesThemes!="") {
			var listeThemes = mesThemes.split(',');
			$("#theme_princ").append(listeThemes[0]);
			for(var key in liste_themes) {
				if (liste_themes[key]==listeThemes[0]) { monidthemeprincipal = key }
			}
			remplit_sel_theme_second(monidthemeprincipal);
			if (listeThemes.length>1){
				$("#theme_second").append(mesThemes.replace(listeThemes[0]+",",""));
				}
		}
}

function remplit_sel_theme_princ() {
	var selectTheme_princ = $('select[name="sel_theme_princ"]');
	selectTheme_princ.empty();
	selectTheme_princ.append('<option selected value="">Thème principal</option>');
	for(var key in liste_themes) {
		selectTheme_princ.append('<option value="'+key+'">'+ liste_themes[key]+'</option>');
	}
}

function remplit_sel_theme_second(id) {
	var selectTheme_second = $('select[name="sel_theme_second"]');
	selectTheme_second.empty();
	selectTheme_second.append('<option selected value="">Thème(s) secondaire(s)</option>');
	for(var key in liste_themes) {
		if (key!=id){
			selectTheme_second.append('<option value="'+key+'">'+ liste_themes[key]+'</option>');
			}
	}
}

function remplit_structures() {
	for(var key in liste_structures) {
		$("#service").append('<option value="'+key+'">'+ liste_structures[key]+'</option>');
		}
}
