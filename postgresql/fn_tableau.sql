-- Function: suivi_etudes.tableau(text, text, text, text, text, text)

-- DROP FUNCTION suivi_etudes.tableau(text, text, text, text, text, text);

CREATE OR REPLACE FUNCTION suivi_etudes.tableau(annee text, dreal text, bop text, service text, pilote text, titre text, avanc text)
  RETURNS SETOF record AS
$BODY$
DECLARE
	enregistrement record; 
	schema text := 'suivi_etudes' ;
	table text := 'liste_etudes' ;
	delai int :=30;
	sql_annee text := '1=1';
	sql_dreal text := '1=1';
	sql_bop text := '1=1';
	sql_service text := '1=1';
	sql_pilote text := '1=1';
	sql_titre text := '1=1';
	sql_avanc text := '1=1';
	sql_global text :='';
	sql_detail text :='';
	sql_having text :='';
	ma_table_A text:='';
	ma_table_B text:='';
BEGIN
--- suivi_etudes.tableau(annee text, dreal text, bop text, service text, pilote text, titre text, avanc text)
--- valeurs possibles pour avancement :
--- 	abandonnee  	when abandon true
--- 	valorisee   	when valorisation_url<>'' OR valorisation_comment<>''
--- 	terminee	when pourcentage_avancement_etude =100
--- 	en_cours	when montant_devis_lb >0 OR ref_devis<>'' OR montant_engagement_lb >0
--- 	programmee	when montant_autorisation_lb >0
---	projet		when montant_demande_lb >0
--- 	idee		si rien 
				
--- version modifiée
--- suppression dans le titre du tiret " - " et du signe " > " pour les BOP
--- appel modifié pour postgresql 9.3 (plus nécessaire de préciser le type de numéric 
--- appel par :
/*

select 	 *
	from suivi_etudes.tableau('','','','','','valorisee')  
	as
	f00(
	 index text,
	 id_etude_hn int,
	 rang varchar(1),
	 pointeur integer,
	 titre varchar(200),
	 service varchar(30),
         pilotage_ddtm varchar(30),
         annee_pgm varchar(10),
         montant_demande_lb numeric,
         montant_autorisation_lb numeric,
         montant_engagement_lb numeric, 
         date_maj varchar(10),
         avanc_etude varchar(10)
         )

*/  

--- définition des requêtes
	if (annee<>'') then 
		sql_annee:='annee_pgm = ''' || annee ||'''';
	end if;
	if (dreal<>'') then 
		sql_dreal:='dreal like ''%' || dreal ||'%''';
	end if;
	if (bop<>'') then 
		sql_bop:='bop = ''' || bop ||'''';
	end if;
	if (service<>'') then 
		sql_service:='lower(service) like lower(''%' || service ||'%'')';
	end if;
	if (pilote<>'') then 
		sql_pilote:='lower(pilotage_ddtm) like lower(''%' || pilote ||'%'')';
	end if;
	if (titre<>'') then 
		sql_titre:='lower(libelle) like lower(''%' || titre ||'%'')';
	end if;
	if (avanc<>'') then 
		sql_avanc:='avanc_etude = ''' || avanc ||'''';
	end if;	
	sql_global:= sql_annee || ' AND ' || sql_dreal || ' AND ' || sql_bop ||' ';
	sql_detail:= sql_annee || ' AND ' || sql_dreal || ' AND ' || sql_bop || ' AND ' || sql_service || ' AND ' || sql_pilote || ' AND ' || sql_titre ||' ';
        sql_having:= sql_annee || ' AND ' || sql_dreal || ' AND ' || sql_bop || ' AND ' || sql_service || ' AND ' || sql_pilote ||' ';
---

ma_table_B := '
	(select * from
		(select
			(dreal || '' - '' || bop || '' - '' || ligne_budgetaire_lb || '' - '' || annee_pgm) as index,
			id_etude_hn,
			''B''::varchar(1) as rang,
			current_date - date(date_maj) - ' || delai ||' as pointeur,
			libelle::varchar(200) as titre,
			service::varchar(30),
			pilotage_ddtm::varchar(30),
			annee_pgm::varchar(10),
			montant_demande_lb,
			montant_autorisation_lb,
			montant_engagement_lb, 
			date_maj::varchar(10),
			case
				when abandon then ''abandonnee''::varchar(10)
				when valorisation_url<>'''' OR valorisation_comment<>'''' then ''valorisee''::varchar(10)
				when pourcentage_avancement_etude =100 then ''terminee''::varchar(10)
				when montant_devis_lb >0 OR ref_devis<>'''' OR montant_engagement_lb >0 then ''en_cours''::varchar(10)
				when montant_autorisation_lb >0 then ''programmee''	::varchar(10)
				when montant_demande_lb >0 then ''projet''::varchar(10)
				else ''idee''::varchar(10)
			end 
			as avanc_etude
		from suivi_etudes.liste_etudes
		WHERE ' || sql_detail ||'
		) as f03
	WHERE ' || sql_avanc || '
	) ';

	
	
ma_table_A := '	
	(select 
		(dreal || '' - '' || bop || '' - '' || ligne_budgetaire_lb || '' - '' || annee_pgm) as index,
		0 as  id_etude_hn,
		''A''::varchar(1) as rang,
		0 as pointeur,
		case 
			when montant_dotation isnull then  (dreal || '' - '' || bop || '' - '' || ligne_budgetaire_lb|| '' - '' || annee_pgm)::varchar(200)
			else
			(dreal || '' - '' || bop || '' - '' || ligne_budgetaire_lb || '' - Dotation '' ||annee_pgm||'' : '' || (montant_dotation::integer) ||'' €'')::varchar(200)
			end 
		as titre, 
		''''::varchar(30) as service, 
		''''::varchar(30) as pilotage_DTM, 
		annee_pgm::varchar(10) as annee_pgm,
		sum(montant_demande_lb) as  "montant demandé",
		sum(montant_autorisation_lb) as  "montant autorisé",
		sum(montant_engagement_lb) as  "montant engagé",
		''''::varchar(10) as date_maj,
		''''::varchar(10) as avanc_etude
	from suivi_etudes.liste_etudes left join suivi_etudes.dotation
	ON
	bop=sigle_bop_dotation and ligne_budgetaire_lb=titre_dotation and annee_pgm=annee_dotation 
	WHERE ' || sql_global ||'
	group by dreal, bop,ligne_budgetaire_lb,montant_dotation, annee_pgm
	)';
	
FOR enregistrement IN execute '
	select * from (
		' || 
		ma_table_A 
		|| ' UNION ' ||
		ma_table_B
		|| ' 
	) as f00
	WHERE index in (
		select distinct index
		from ' || ma_table_B || ' as f01)
	order by index, rang,titre
	' 
LOOP
	RETURN NEXT enregistrement;
END LOOP;
RAISE NOTICE '%', ma_table_A;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
ALTER FUNCTION suivi_etudes.tableau(text, text, text, text, text, text, text)
  OWNER TO postgres;
