-- Function: suivi_etudes.creation_tables_services(text)

-- DROP FUNCTION suivi_etudes.creation_tables_services(text);

CREATE OR REPLACE FUNCTION suivi_etudes.creation_tables_services(monschema text)
  RETURNS void AS
$BODY$
DECLARE

BEGIN
-- select * from suivi_etudes.creation_tables_services('suivi_etudes')
EXECUTE'
	DROP TABLE IF EXISTS '|| monschema ||'.liste_services CASCADE;
	
	CREATE TABLE '|| monschema ||'.liste_services
	(
	  id_service serial,
	  service character varying,
	  direction boolean DEFAULT false, -- VRAI Si Direction (Structure)...
	  CONSTRAINT id_service_clef PRIMARY KEY (id_service)
	)
	WITH (
	  OIDS=FALSE
	);

	ALTER TABLE '|| monschema ||'.liste_services OWNER TO postgres;
	GRANT ALL ON TABLE '|| monschema ||'.liste_services TO gestdoc;
	COMMENT ON TABLE '|| monschema ||'.liste_services IS ''Liste des services'';

	INSERT INTO '|| monschema ||'.liste_services (service,direction) VALUES
		(''SMCAP'',false),
		(''SECLAD'',false),
		(''SRN'',false),
		(''SRI'',false),
		(''SSTV'',false),
		(''SMI'',false),
		(''Mission Estuaire'',false),
		(''Mission Mont-St-Michel'',false),
		(''DDTM14'',true),
		(''DDTM27'',true),
		(''DDTM50'',true),
		(''DDT61'',true),
		(''DDTM76'',true),
		(''DDCS14'',true),
		(''DDCS27'',true),
		(''DDCS50'',true),
		(''DDCSPP61'',true),
		(''DDCS76'',true),
		(''DIRNO'',true),
		(''DIRM'',true);
	';
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION suivi_etudes.creation_tables_services(text)
  OWNER TO postgres;
