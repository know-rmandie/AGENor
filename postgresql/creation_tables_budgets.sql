-- Function: suivi_etudes.creation_tables_budgets(text)

-- DROP FUNCTION suivi_etudes.creation_tables_budgets(text);

CREATE OR REPLACE FUNCTION suivi_etudes.creation_tables_budgets(monschema text)
  RETURNS void AS
$BODY$
DECLARE

BEGIN
-- select * from suivi_etudes.creation_tables_budgets('suivi_etudes')
EXECUTE'
	DROP TABLE IF EXISTS '|| monschema ||'.liste_budgets CASCADE;
	
	CREATE TABLE '|| monschema ||'.liste_budgets
	(
	  id character varying,
	  nom character varying,
	  bop character varying,
	  action boolean,
	  gestionnaire character varying,
	  CONSTRAINT id_budget_clef PRIMARY KEY (id)
	)
	WITH (
	  OIDS=FALSE
	);

	ALTER TABLE '|| monschema ||'.liste_budgets OWNER TO postgres;
	GRANT ALL ON TABLE '|| monschema ||'.liste_budgets TO gestdoc;
	COMMENT ON TABLE '|| monschema ||'.liste_budgets IS ''BOP et Gestionnaire après 2016'';

	INSERT INTO '|| monschema ||'.liste_budgets (id,bop,nom,gestionnaire,action) VALUES
		(''113'',''PEB'',''Paysages, eau et biodiversité'',''SRE'',false),
		(''113-01'',''PEB'',''Sites, paysages, publicité'',''SRE'',true),
		(''113-07'',''PEB'',''Gestion des milieux et biodiversité'',''SRE'',true),
		(''135'',''UTAH'',''Urbanisme, territoires et amélioration de l''''habitat'',''SECLAD'',false),
		(''135-04-03'',''UTAH'',''Qualité de la construction : études'',''SECLAD'',true),
		(''135-05-06'',''UTAH'',''Observation, études et évaluation : études locales'',''SECLAD'',true),
		(''135-05-04'',''UTAH'',''Applications informatiques nationales (dont numérisation SUP et doc d’urba – GPU)'',''SECLAD'',true),
		(''135-07-01'',''UTAH'',''Villes et territoires durables'',''SECLAD'',true),
		(''174'',''ECAM'',''Énergie, climat et après-mines'',''SECLAD'',false),
		(''174-05'',''ECAM'',''Lutte contre le changement climatique'',''SECLAD'',true),
		(''181'',''Risques'',''Prévention des risques'',''SRI'',false),
		(''181-01'',''Risques'',''Prévention des risques technologiques et des pollutions'',''SRI'',true),
		(''181-10'',''Risques'',''Prévention des risques naturels et hydrauliques'',''SRI'',true),
		(''181-11'',''Risques'',''Gestion de l''''après-mine et travaux de mise en sécurité, indemnisations et expropriations sur les sites'',''SRI'',true),
		(''203'',''IST'',''Infrastructures et services de transports'',''SMI'',false),
		(''203-10'',''IST'',''Infrastructures de transports collectifs et ferroviaires'',''SMI'',true),
		(''203-13'',''IST'',''Soutien, régulation, contrôle et sécurité des services de transports terrestres'',''SMI'',true),
		(''203-15'',''IST'',''Stratégie et soutien'',''SMI'',true),
		(''207'',''SR'',''Sécurité et éducation routières'',''SSTV'',false),
		(''217'',''CPPEDMD'',''Conduite et pilotage des politiques de l''''écologie, du développement et de la mobilité durables'',''SMCAP'',false),
		(''217-01'',''CPPDDM'',''Stratégie, expertise et études en matière de développement durable'',''SMCAP'',true),
		(''09'',''DAP CEREMA'',''DAP CEREMA '',''SMCAP'',false),
		(''09-PP'',''DAP CEREMA'',''Pré-Programmé'',''SMCAP'',true),
		(''09-T9+ - ERI'',''DAP CEREMA'',''Enveloppe régionale indifférenciée (ERI)'',''SMCAP'',true),
		(''09-T9+ - DGALN'',''DAP CEREMA'',''Enveloppe DGALN'',''SMCAP'',true),
		(''09-T9+ - DGITM'',''DAP CEREMA'',''Enveloppe DGITM'',''SMCAP'',true);
	';
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION suivi_etudes.creation_tables_budgets(text)
  OWNER TO postgres;
