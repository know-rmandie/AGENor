CREATE OR REPLACE FUNCTION creation_table_etudes(schem text, prop text, deleg text)
    RETURNS void AS
$BODY$
DECLARE
    schem text
    prop text
    deleg text
BEGIN

CREATE TABLE schem.liste_etudes (
	id_etude_hn int4 NOT NULL,
	dreal varchar NULL,
	bop varchar NULL,
	service varchar NULL,
	pilotage_ddtm varchar NULL,
	libelle varchar NULL,
	commentaires varchar NULL,
	date_maj timestamp NULL,
	ligne_budgetaire_lb varchar NULL,
	date_demande_lb_n1 timestamp NULL,
	date_demande_lb_n timestamp NULL,
	date_autorisation_lb timestamp NULL,
	date_devis_lb timestamp NULL,
	date_engagement_lb timestamp NULL,
	date_facturation_lb timestamp NULL,
	montant_demande_lb_n1 numeric(8,2) NULL,
	montant_demande_lb_n numeric(8,2) NULL,
	montant_autorisation_lb numeric(8,2) NULL,
	montant_devis_lb numeric(8,2) NULL,
	montant_engagement_lb numeric(8,2) NULL,
	montant_facturation_lb numeric(8,2) NULL,
	ligne_budgetaire_lb2 varchar NULL,
	date_demande_lb2_n1 timestamp NULL,
	date_demande_lb2_n timestamp NULL,
	date_autorisation_lb2 timestamp NULL,
	date_devis_lb2 timestamp NULL,
	date_engagement_lb2 timestamp NULL,
	date_facturation_lb2 timestamp NULL,
	montant_demande_lb2_n1_bak varchar NULL,
	montant_demande_lb2_n_bak varchar NULL,
	montant_autorisation_lb2 numeric(8,2) NULL,
	montant_devis_lb2 numeric(8,2) NULL,
	montant_engagement_lb2 numeric(8,2) NULL,
	montant_facturation_lb2 numeric(8,2) NULL,
	date_demande_devis timestamp NULL,
	date_reception_devis timestamp NULL,
	date_verification_devis timestamp NULL,
	date_notification_devis timestamp NULL,
	date_debut_etude timestamp NULL,
	date_fin_etude timestamp NULL,
	date_demande_facture timestamp NULL,
	date_reception_facture timestamp NULL,
	date_transmission_facture timestamp NULL,
	date_acquittee_facture timestamp NULL,
	annee_pgm varchar NULL,
	pourcentage_avancement_etude int4 NULL,
	montant_demande_lb numeric(8,2) NULL,
	montant_demande_lb2 numeric(8,2) NULL,
	date_demande_lb timestamp NULL,
	date_demande_lb2 timestamp NULL,
	montant_demande_lb2_n1 numeric(8,2) NULL,
	montant_demande_lb2_n numeric(8,2) NULL,
	valorisation_comment varchar NULL,
	valorisation_url varchar NULL,
	departement_cete varchar NULL,
	nom_bureau_etude varchar NULL,
	contact_bureau_etude varchar NULL,
	ref_devis varchar NULL,
	abandon bool NULL,
	themes varchar NULL,
	pil_contact varchar NULL,
	zone_geo varchar NULL,
	priorite int4 NULL,
	CONSTRAINT liste_etudes_pkey PRIMARY KEY (id_etude_hn)
)
WITH (
	OIDS=FALSE
);

ALTER TABLE schem.liste_etudes OWNER TO prop;
GRANT ALL ON TABLE schem.liste_etudes TO deleg;
COMMENT ON TABLE schem.liste_etudes IS ''Liste complète des études'';

END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
