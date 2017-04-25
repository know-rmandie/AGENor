<?php
	class MesEtudes {
		// paramètres de connexion
		private $_ip_serv;
		private $_nom_bd;
		private $_user;
		private $_mdp;
		private $_dsn;
		private $_connexion;
		// alias des noms des tables dans la base de données
		private $_schema;
		private $_bop;
		private $_dotation;
		private $_droits;
		private $_etudes;
		private $_bilan;
		private $_budgets;
		private $_thematiques;
		private $_services;
		private $_id_etude;
		// liste des champs table études
		private $_champs_general;
		private $_champs_financement;
		private $_champs_devis;
		private $_champs_compta;
		private $_champs_avanc_devis;
		private $_champs_avanc_etude;
		private $_champs_avanc_facturation;
		private $_champs_valorisation;
		public  $_liste_champs_etude ;
		public  $_champs_creation ;
		// liste des champs table droits
		private $_champ_autorisation;
		private $_champ_utilisateur;
		private $_champ_mdp;
		private $_champ_login;
		private $_champ_service;
		// paramètres etudes
		public  $_annee_0;

		// info sur la connexion
		private $_message;
		private $_conn_ok; // true --> 1 connexion réussie

		public function __construct() {
					$this->MesEtudes();
			}

		public function MesEtudes(){
		// initialise les paramètres à partir du fichier ini
			$mes_parametres = parse_ini_file("param.ini");
			$this->_ip_serv=$mes_parametres['ip'];
			$this->_nom_bd=$mes_parametres['base_etudes'];
			$this->_user=$mes_parametres['login'];
			$this->_mdp=$mes_parametres['mdp'];
			$this->_dsn='pgsql:host='.$this->_ip_serv.';dbname='.$this->_nom_bd;
			try {
				$this->_connexion = new PDO($this->_dsn, $this->_user, $this->_mdp);
				$this->_message = 'Connection OK';
				$this->_conn_ok = true;
				}
				catch (PDOException $e)
					{
				$this->_message = 'Connection failed: ' . $e->getMessage() ;
				$this->_conn_ok = false;
					} ;
			$this->_schema=$mes_parametres['schema'];
			$this->_bop=$mes_parametres['bop'];
			$this->_dotation=$mes_parametres['dotation'];
			$this->_droits=$mes_parametres['droits'];
			$this->_etudes=$mes_parametres['etudes'];
			$this->_bilan=$mes_parametres['bilan'];
			$this->_budgets=$mes_parametres['budgets'];
			$this->_thematiques=$mes_parametres['thematiques'];
			$this->_services=$mes_parametres['services'];
			$this->_id_etude=$mes_parametres['id_etude'];

			$this->_champs_general=$mes_parametres['champs_general'];
			$this->_champs_financement=$mes_parametres['champs_financement'];
			$this->_champs_devis=$mes_parametres['champs_devis'];
			$this->_champs_compta=$mes_parametres['champs_compta'];
			$this->_champs_avanc_devis=$mes_parametres['champs_avanc_devis'];
			$this->_champs_avanc_etude=$mes_parametres['champs_avanc_etude'];
			$this->_champs_avanc_facturation=$mes_parametres['champs_avanc_facturation'];
			$this->_champs_valorisation=$mes_parametres['champs_valorisation'];

			$this->_liste_champs_etude = $this->_champs_general .",";
		    $this->_liste_champs_etude .= $this->_champs_financement .",";
		    $this->_liste_champs_etude .= $this->_champs_devis .",";
		    $this->_liste_champs_etude .= $this->_champs_compta  .",";
		    $this->_liste_champs_etude .= $this->_champs_avanc_devis  .",";
		    $this->_liste_champs_etude .= $this->_champs_avanc_etude  .",";
		    $this->_liste_champs_etude .= $this->_champs_avanc_facturation  .",";
			$this->_liste_champs_etude .= $this->_champs_valorisation ;

			$this->_champs_creation=$mes_parametres['champs_creation'];

			$this->_champ_autorisation=$mes_parametres['champ_autorisation'];
			$this->_champ_utilisateur=$mes_parametres['champ_utilisateur'];
			$this->_champ_mdp=$mes_parametres['champ_mdp'];
			$this->_champ_login=$mes_parametres['champ_login'];
			$this->_champ_service=$mes_parametres['champ_service'];

			$this->_annee_0=$mes_parametres['annee_0'];
		}

		public function get_value($alias) { // Renvoi la valeur de l'alias
			return $this->{"_$alias"};
		}
	// section fixe - applicable sans modification à tous les projets

		public function get_conn() { // return  1 si connexion ok, sinon 0;
			return $this->_conn_ok ;
		}

		public function get_nomtable($nom_table) { // renvoi le nom réel de la table à partir de son alias
			return  $this->{"_$nom_table"} ;
		}

		public function get_nomtablecomplet($nom_table) { // renvoi le nom réel de la table avec le schéma à partir de son alias
			return  $this->_schema.'.'.$this->{"_$nom_table"} ;
		}

		public function get_nom_champ_id_etude() {  // renvoi le nom du champ id_etude
			return $this->_id_etude ;
		}

		public function get_message() {  // renvoi le type d'erreur de connexion ou ok
			return $this->_message;
		}

		public function exec_requete($sql) {
			$result = $this->_connexion->prepare($sql) ;
			$result->execute();
			return $result ;
		}


		public function get_table($selection,$nom_table,$where) { // récupère la table d'alias $nom_table sur le serveur
			$result = $this->_message ;
			if ($this->_conn_ok) {
				if ($this->_schema=='') {
					$marequete="select ".$selection." from " ;
					}
					else {
					$marequete="select ".$selection." from ".$this->_schema.".";
					};
				$marequete .= $this->{"_$nom_table"} .' '.$where;
				$result = $this->_connexion->prepare($marequete) ;
				$result->execute();
				}
			return $result ;
		}

		public function affiche_table($selection,$larg_table,$nom_table,$where) {
            /* affiche le contenu HTML à mettre entre les balises <table> et </table>
                $selection est la liste des champs à prendre
                $where est la condition SQL
            */
			$result = $this->get_table($selection,$nom_table,$where) ;
			if ($result <> $this->_message) {
				$table='<thead>';
				$dimtable=explode(",", $larg_table);

				$row = $result->fetch(PDO::FETCH_ASSOC);
				$temp = '<tr><th width="AA">' . implode('</th><th width="AA">', array_keys($row)). '</th></tr>';
				for ($i=0;$i< count($dimtable);$i++){
					$temp = preg_replace('/width="AA"/', 'width="'.$dimtable[$i].'"', $temp, 1);
				}
				$table .=  $temp."\n" ;
				$table .=  '</thead>';
				do {
					$temp =  '<tr><td  width="AA">'. implode('</td><td width="AA">', $row). '</td></tr>' ;
					for ($i=0;$i< count($dimtable);$i++){
						$temp = preg_replace('/width="AA"/', 'width="'.$dimtable[$i].'"', $temp, 1);
					}
					$table .=  $temp."\n" ;
				} while($row = $result->fetch(PDO::FETCH_ASSOC));
			}
			else {
			$table ='<p>'.$this->_message ;
			} ;
			return $table;
		}

		public function affiche_select($selection,$nom_table,$where) {
        /* affiche le contenu HTML à mettre entre les balises <select> et </select>
            $selection comprend le value et le libellé
            $where est la condition SQL
			*/
			$result = $this->get_table($selection,$nom_table,$where) ;
			if ($result <> $this->_message) {
				$row = $result->fetch(PDO::FETCH_ASSOC);
				$MySelect = "" ;
				do {
					$MySelect .='<option value="'. implode('">', $row).'</option>'."\n" ;
					} while($row = $result->fetch(PDO::FETCH_ASSOC));
				}
				else {
				$MySelect='<p>'.$this->_message ;
				} ;
			return $MySelect;
		}

		// section droit utilisateur
		public function get_droit($login,$mdp){
			$champs ='';
		    $champs .= $this->_champ_autorisation .",";
		    $champs .= $this->_champ_utilisateur .",";
		    $champs .= $this->_champ_service ;
			$condition_where="WHERE ". $this->_champ_login ."='".$login."' AND ". $this->_champ_mdp."='".$mdp."'";
			$result = $this->get_table($champs,"droits",$condition_where);
			$temp = '' ;
			$row = $result->fetch(PDO::FETCH_ASSOC) ;
			// $sortie =implode('|',array_keys($row)) . "[!]";
			$sortie="T_autorisation|T_utilisateur|T_service". "[!]";
			do {
				$temp =   implode('|', $row) ;
				$sortie .=  $temp. "[!]" ;
			} while($row = $result->fetch(PDO::FETCH_ASSOC))
			;
			return $sortie;
		}

		// section suivi des études
		public function get_etude($id){
			$result = $this->get_table($this->_liste_champs_etude,"etudes","WHERE ". $this->_id_etude ."=".$id );
			$temp = '' ;
			$row = $result->fetch(PDO::FETCH_ASSOC) ;
			$sortie =implode('|',array_keys($row)) . "[!]";
			do {
				$temp =   implode('|', $row) ;
				$sortie .=  $temp. "[!]" ;
			} while($row = $result->fetch(PDO::FETCH_ASSOC))
			;
			return $sortie;
		}

		public function lit_table_etude($annee,$dreal,$bop,$pilote,$titre,$avanc,$condition) {
			// fonction postgresql :
			// suivi_etudes.tableau(annee text, dreal text, bop text, pilote text, titre text, avanc text)
			if ($condition==''){
					$where ='';
					} else {
					$where =' WHERE '.$condition;
					}
			$marequete =  "select 	 *
							as
							f00(
							 index text,
							 id_etude int,
							 rang varchar(1),
							 pointeur integer,
							 titre varchar(200),
							 service varchar(30),
							 pilotage varchar(30),
							 annee varchar(10),
							 demande numeric,
							 autorisation numeric,
							 engagement numeric,
							 date_maj varchar(10),
							 avanc_etude varchar(10)
								 ) " . $where .";
							";
			$sortie = '' ;
			$temp ='';
			if ($this->_conn_ok) {
				$result = $this->_connexion->prepare($marequete) ;
				$result->execute();

				while($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$temp =   implode('|', $row) ;
					$sortie .=  $temp."\n" ;
				} ;

			}
			return $sortie ;
		}

		public function table_etudes($annee,$dreal,$bop,$service,$pilote,$titre,$avanc,$themes) {
			// fonction postgresql :
			// suivi_etudes.tableau(annee text, dreal text, bop text, pilote text, titre text, avanc text, themes text)
			$marequete =   "select 	 *
							from ".$this->_schema.".tableau_format('$annee','$dreal','$bop','$service','$pilote','$titre','$avanc','$themes')
							as
							f00(
							 index text,
							 id_etude int,
							 rang varchar(1),
							 pointeur integer,
							 titre varchar(200),
							 service varchar(30),
							 pilotage varchar(30),
							 annee varchar(10),
							 demande varchar(20),
							 autorisation varchar(20),
							 engagement varchar(20),
							 date_maj varchar(10),
							 avanc_etude varchar(10)
								 ) ;
							";
			$result = $this->_message ;
			if ($this->_conn_ok) {
				$result = $this->_connexion->prepare($marequete) ;
				$result->execute();

				//dessin de la table
				$table_foot='<tfoot>'; // pourquoi un tfoot?
				$table ='<table id="liste_etudes">';
				$table .='<thead>';

				$row = $result->fetch(PDO::FETCH_ASSOC);
			    // array_keys prend les entêtes des colonnes
				$table .= '<tr class="cl_entete_tab_etudes"><th>symbole</th><th>' . implode('</th><th>',array_keys($row)). '</th></tr>';
				$table_foot.='<tr class="cl_entete_tab_etudes" id="tab_complet"><th>symbole</th><th>' . implode('</th><th>',array_keys($row)). '</th></tr>';
				$table .=  '</thead>';
				$table_foot.='</tfoot>';
				// lecture de la table et mise en place des class et id pour les css
				$cpt='';
				do {
					$temp =  '<tr class="_class" id="_id" onclick="affiche_detail(this)"><td class="noBef cl_avanc_etude_"><span class="fa cl_avanc_etude_"></span></td><td>'. implode('</td><td>', $row). '</td></tr>' ;
					if ($row["rang"]=='A') {
						$temp = preg_replace('/_class/', 'cl_groupe_etudes impair', $temp, 1);
						$cpt='pair';
					} else {
						$temp = preg_replace('/_class/', 'cl_groupe_ligne '.$cpt, $temp, 1);
 						if ($cpt=='pair') {
							$cpt='impair' ;
							} else {
							$cpt='pair';
							}
					}
					// table de correspondance entre avancements et classes font-awesome
					$avanc_fontA = array('idee' => 'fa-lightbulb-o','projet' => 'fa-tasks','programmee' => 'fa-eur','en_cours' => 'fa-arrow-right', 'terminee' => 'fa-check', 'valorisee' => 'fa-book', 'abandonnee' => 'fa-times', '' => '');
					// substitution dans la table
// !todo : pourquoi ne met-on pas directement cette valeur dans la table?
					$temp = preg_replace('/cl_avanc_etude_/', $avanc_fontA[$row["avanc_etude"]], $temp);
					$temp = preg_replace('/_id/', $row["id_etude"], $temp, 1);
					$table .=  $temp."\n" ;
				} while($row = $result->fetch(PDO::FETCH_ASSOC));
				$table .=$table_foot;
				$table .="</table>";
				$result = $table ;
				}
			return $result ;
		}

		public function get_bop() {
		/*
		retourne un tableau associatif du type :
			{
			"id_bop_1" : ["nom","bop","gestionnaire", {
													"id_action_1" : ["nom","gestionnaire"],
													..
													"id_action_n" : ["nom","gestionnaire"]
													}],
			....
			"id_bop_n" : ["nom","bop","gestionnaire", {
													"id_action_1" : ["nom","gestionnaire"],
													..
													"id_action_n" : ["nom","gestionnaire"]
													}]
			}
		*/
			$mon_bop="select * from ".$this->get_nomtablecomplet('budgets')." where not action";
			$montableau="{}";
			$mes_bop = array();

			if ($this->_conn_ok) {
				$montableau="{";
				$result_bop = $this->exec_requete($mon_bop) ;

				while($row_bop = $result_bop->fetch(PDO::FETCH_ASSOC)) {
					$mes_bop[$row_bop['id']]='"'.$row_bop['nom'].'","'.$row_bop['gestionnaire'].'"';
					}


				foreach ($mes_bop as $code => $value) {
					$detail_bop ="select * from ".$this->get_nomtablecomplet('budgets')." where id like '".$code."%' AND action=true";
					$result_detail_bop = $this->exec_requete($detail_bop) ;
					$montableau .=',"'.$code.'":[';
					$montableau .=$value.',{';
					while($row_detail_bop = $result_detail_bop->fetch(PDO::FETCH_ASSOC)) {
						$montableau .=',"'.$row_detail_bop['id'].'":["'.$row_detail_bop['nom'].'","'.$row_detail_bop['gestionnaire'].'"]';
						} ;
					$montableau .='}]';
				}
				$montableau .='}';
				$montableau=str_replace("{,","{",$montableau);

			}
			return $montableau;
		}

		public function get_thematiques() {
		/*
		retourne un tableau associatif du type :
			{
			"id_theme_1" : "theme_1",
			....
			"id_theme_n" : "theme_n",
			}
		*/
			$mes_themes="select * from ".$this->get_nomtablecomplet('thematiques')." order by theme";
			$montableau="{}";

			if ($this->_conn_ok) {
				$montableau="{";
				$result_themes = $this->exec_requete($mes_themes) ;
				while($row_themes = $result_themes->fetch(PDO::FETCH_ASSOC)) {
					$montableau .=',"'.$row_themes['id_theme'].'":"'.$row_themes['theme'].'"';
					} ;
				$montableau .='}';
				$montableau=str_replace("{,","{",$montableau);
			}
			return $montableau;
			//return 	$mes_themes ;
		}

		public function get_structures() {
		/*
		retourne le tableau des directions (structures) :
			{
			"structure_1" : "structure_1",
			....
			"structure_n" : "structure_n",
			}
		*/
			$mes_structures="select * from ".$this->get_nomtablecomplet('services')." WHERE direction=true order by service";
			$montableau="{}";

			if ($this->_conn_ok) {
				$montableau="{";
				$result_structures = $this->exec_requete($mes_structures) ;
				while($row_structures = $result_structures->fetch(PDO::FETCH_ASSOC)) {
					$montableau .=',"'.$row_structures['service'].'":"'.$row_structures['service'].'"';
					} ;
				$montableau .='}';
				$montableau=str_replace("{,","{",$montableau);
			}
			return $montableau;
		}
	}


?>
