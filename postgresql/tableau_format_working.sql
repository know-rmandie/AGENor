CREATE OR REPLACE FUNCTION suivi_etudes.tableau_format(annee text, dreal text, bop text, service text, pilote text, titre text, avanc text, themes text)
 RETURNS SETOF record
 LANGUAGE plpgsql
AS $function$
DECLARE
	enregistrement record;
	mon_schema text := 'suivi_etudes' ;
	ma_table text := 'liste_etudes' ;
	mon_theme text :='';
	delai int :=30;
	sql_annee text := '1=1';
	sql_dreal text := '1=1';
	sql_bop text := '1=1';
	sql_service text := '1=1';
	sql_pilote text := '1=1';
	sql_titre text := '1=1';
	sql_avanc text := '1=1';
	sql_themes text := '1=1';
	sql_global text :='';
	sql_detail text :='';
	sql_having text :='';
	ma_table_A text:='';
	ma_table_B text:='';
BEGIN

--- suivi_etudes.tableau_format(annee text, dreal text, bop text, service text, pilote text, titre text, avanc text, themes text)
--- valeurs possibles pour avancement :
--- 	abandonnee  	when abandon true
--- 	valorisee   	when valorisation_url<>'' OR valorisation_comment<>''
--- 	terminee	when pourcentage_avancement_etude =100
--- 	en_cours	when montant_devis_lb >0 OR ref_devis<>'' OR montant_engagement_lb >0
--- 	programmee	when montant_autorisation_lb >0
---		projet		when montant_demande_lb >0
--- 	idee		si rien

--- version modifiée
--- suppression dans le titre du tiret " - " et du signe " > " pour les BOP
--- appel modifié pour postgresql 9.3 (plus nécessaire de préciser le type de numéric
--- modification du 28-11-2016 pour intégrer le champ themes
--- appel par :
/*

select 	 *
	from suivi_etudes.tableau_format('2016', 'SECLAD', '', '', '', '', '','2')
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
         montant_demande_lb varchar(20),
         montant_autorisation_lb varchar(20),
         montant_engagement_lb varchar(20),
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
	if (themes<>'') then
		EXECUTE 'Select theme from ' || mon_schema || '.liste_themes where id_theme=' || themes into mon_theme ;
		sql_themes:='themes like ''%' || mon_theme ||'%''';
	end if;
	sql_global:= sql_annee || ' AND ' || sql_dreal || ' AND ' || sql_bop ||' ';
	sql_detail:= sql_annee || ' AND ' || sql_dreal || ' AND ' || sql_bop || ' AND ' || sql_service || ' AND ' || sql_pilote || ' AND ' || sql_titre || ' AND ' || sql_themes ||' ';
        sql_having:= sql_annee || ' AND ' || sql_dreal || ' AND ' || sql_bop || ' AND ' || sql_service || ' AND ' || sql_pilote ||' ';


ma_table_B := '
	(select * from
		(select
			(bop || '' - '' || ligne_budgetaire_lb || '' - '' || annee_pgm) as index,
			id_etude_hn,
			''B''::varchar(1) as rang,
			current_date - date(date_maj) - ' || delai ||' as pointeur,
			libelle::varchar(200) as titre,
			service::varchar(30),
			pilotage_ddtm::varchar(30),
			annee_pgm::varchar(10),
			replace(to_char(montant_demande_lb, ''999,999,999''),'','',''&nbsp;'')::varchar(20),
			replace(to_char(montant_autorisation_lb, ''999,999,999''),'','',''&nbsp;'')::varchar(20),
			replace(to_char(montant_engagement_lb, ''999,999,999''),'','',''&nbsp;'')::varchar(20),
			date_maj::varchar(10),
			case
				when abandon then ''abandonnee''::varchar(10)
				when valorisation_url<>'''' OR valorisation_comment<>'''' then ''valorisee''::varchar(10)
				when pourcentage_avancement_etude =100 then ''terminee''::varchar(10)
				when montant_devis_lb >0 OR ref_devis<>'''' OR montant_engagement_lb >0 OR pourcentage_avancement_etude >0 then ''en_cours''::varchar(10)
				when montant_autorisation_lb >0 then ''programmee''	::varchar(10)
				when montant_demande_lb >0 then ''projet''::varchar(10)
				else ''idee''::varchar(10)
			end
			as avanc_etude
		from ' || mon_schema || '.' ||ma_table ||'
		WHERE ' || sql_detail ||'
		) as f03
	WHERE ' || sql_avanc || '
	) ';



ma_table_A := '
	(select
		(bop || '' - '' || ligne_budgetaire_lb || '' - '' || annee_pgm) as index,
		0 as id_etude_hn,
		''A''::varchar(1) as rang,
		0 as pointeur,
		case
			when montant_dotation isnull then  (bop || '' - '' || ligne_budgetaire_lb|| '' - '' || annee_pgm)::varchar(200)
		else
			(bop || '' - '' || ligne_budgetaire_lb || '' - Dotation '' ||annee_pgm||'' : '' || (montant_dotation::integer) ||'' €'')::varchar(200)
		end
		as titre,
		''''::varchar(30) as service,
		''''::varchar(30) as pilotage_DTM,
		annee_pgm::varchar(10) as annee_pgm,
		replace(to_char(sum(montant_demande_lb), ''999,999,999''),'','',''&nbsp;'')::varchar(20) as  "montant demandé",
		replace(to_char(sum(montant_autorisation_lb), ''999,999,999''),'','',''&nbsp;'')::varchar(20) as  "montant autorisé",
		replace(to_char(sum(montant_engagement_lb), ''999,999,999''),'','',''&nbsp;'')::varchar(20) as  "montant engagé",
		''''::varchar(10) as date_maj,
		''''::varchar(10) as avanc_etude
	from suivi_etudes.liste_etudes left join suivi_etudes.dotation
	ON
	ligne_budgetaire_lb=commentaires_dotation and annee_pgm=annee_dotation
	WHERE ' || sql_global ||'
	group by dreal, bop, ligne_budgetaire_lb, montant_dotation, annee_pgm
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
$function$
