-- Function: suivi_etudes.creation_tables_territoires(text)

-- DROP FUNCTION suivi_etudes.creation_tables_territoires(text);

CREATE OR REPLACE FUNCTION suivi_etudes.creation_tables_territoires(monschema text)
  RETURNS void AS
$BODY$
DECLARE

BEGIN
-- select * from suivi_etudes.creation_tables_territoires('suivi_etudes')
EXECUTE'
	DROP TABLE IF EXISTS '|| monschema ||'.liste_territoires CASCADE;
	
	CREATE TABLE '|| monschema ||'.liste_territoires
	(
	  id_territoire serial,
	  territoire character varying,
	  CONSTRAINT id_territoire_clef PRIMARY KEY (id_territoire)
	)
	WITH (
	  OIDS=FALSE
	);

	ALTER TABLE '|| monschema ||'.liste_territoires OWNER TO postgres;
	GRANT ALL ON TABLE '|| monschema ||'.liste_territoires TO gestdoc;
	COMMENT ON TABLE '|| monschema ||'.liste_territoires IS ''Liste des territoires'';

	INSERT INTO '|| monschema ||'.liste_territoires (territoire) VALUES
		(''Normandie''),
		(''Calvados''),
		(''Eure''),
		(''Manche''),
		(''Orne''),
		(''Seine-Maritime''),
		(''Rouen''),
		(''Le Havre''),
		(''Caen''),
		(''Cherbourg''),
		(''Evreux''),
		(''Dieppe''),
		(''Saint-Lô''),
		(''Alençon''),
		(''Autre (précisez)'');
	';

END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION suivi_etudes.creation_tables_territoires(text)
  OWNER TO postgres;
