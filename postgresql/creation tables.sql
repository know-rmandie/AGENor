-- Function: suivi_etudes.creation_tables(text)

-- DROP FUNCTION suivi_etudes.creation_tables(text);

CREATE OR REPLACE FUNCTION suivi_etudes.creation_tables(schema text)
  RETURNS void AS
$BODY$
DECLARE

BEGIN
-- select * from suivi_etudes.creation_tables('suivi_etudes')
	PERFORM * from suivi_etudes.creation_tables_services(schema);
	PERFORM * from suivi_etudes.creation_tables_territoires(schema);
	PERFORM * from suivi_etudes.creation_tables_themes(schema);
	PERFORM * from suivi_etudes.creation_tables_budgets(schema);
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION suivi_etudes.creation_tables(text)
  OWNER TO postgres;
