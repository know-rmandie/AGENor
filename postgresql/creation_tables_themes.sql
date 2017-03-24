-- Function: suivi_etudes.creation_tables_themes(text)

-- DROP FUNCTION suivi_etudes.creation_tables_themes(text);

CREATE OR REPLACE FUNCTION suivi_etudes.creation_tables_themes(monschema text)
  RETURNS void AS
$BODY$
DECLARE

BEGIN
-- select * from suivi_etudes.creation_tables_themes('suivi_etudes')
EXECUTE'
	DROP TABLE IF EXISTS '|| monschema ||'.liste_themes CASCADE;
	
	CREATE TABLE '|| monschema ||'.liste_themes
	(
	  id_theme serial,
	  theme character varying,
	  CONSTRAINT id_theme_clef PRIMARY KEY (id_theme)
	)
	WITH (
	  OIDS=FALSE
	);

	ALTER TABLE '|| monschema ||'.liste_themes OWNER TO postgres;
	GRANT ALL ON TABLE '|| monschema ||'.liste_themes TO gestdoc;
	COMMENT ON TABLE '|| monschema ||'.liste_themes IS ''Liste des thèmes DREAL, DDTM et CETE'';

	INSERT INTO '|| monschema ||'.liste_themes (theme) VALUES
		(''Air''),
		(''Aménagement''),
		(''Biodiversité''),
		(''Construction''),
		(''Développement durable''),
		(''Eau''),
		(''Energie''),
		(''Habitat''),
		(''Information et participation des citoyens''),
		(''Infrastructures''),
		(''Mer Littoral''),
		(''Mobilité''),
		(''Risques naturels''),
		(''Risques technologiques''),
		(''Santé environnement''),
		(''Sites et Paysages''),
		(''Sécurité routière''),
		(''Transports'');
	';
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION suivi_etudes.creation_tables_themes(text)
  OWNER TO postgres;
