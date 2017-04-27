-- Création des tables
CREATE OR REPLACE FUNCTION tables(schema proprietaire delegue text)
  RETURNS void AS
$BODY$
DECLARE

BEGIN
    PERFORM * from suivi_etudes.creation_tables_etudes(schema proprietaire delegue);
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
