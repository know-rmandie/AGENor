# Structure de la base de données
*version future de la base de données avec les références à l'[ancienne structure de la base](database.md) et pour être en conformité avec le projet de [fiche étude](fiche-etude.md)*

## Table Etudes [E]
Cette table présente les études et prestations devant faire l'objet d'une programmation.

| champ | description | type | valeurs | ant. | commentaire |
| ----- | ----------- | :--: | :-----: | :--: | ----------- |
| | __*Généralités*__ |
| id | un identifiant unique pour l'étude | char | `aaaamm-x0x` | *id_etude_hn* | généré à la création de la fiche 
an_prog | l'année de programmation de l'étude. Pour les idées et projets d'études, par défaut cette année est l'année suivante à la date de création de la fiche | char | `aaaa` | *annee_pgm* |
titre | un titre explicite pour l'étude / la prestation | char | - | *libelle* | repris dans CIRCE
type | le type de prestation (étude, AMO, ...) | char | `{Types}` | *-* | 
d_maj | date de la mise à jour de la fiche | timestamp | `=now()` | *date_maj* | 
|| __*Description*__ |
themes | la (les) thématique(s) de l'étude, sélectionnée(s) dans le thésaurus à disposition. Par ordre d'importance. Le premier thème est le thème principal de l'étude | char | `[T][nom],` | *{Themes}* | repris dans CIRCE, séparés par des virgules
mots_cles | une liste de mots clés libre, complémentaires de la (des) thématique(s) | char | - | *-* | repris dans CIRCE, séparés par des virgules
zone_geo | le périmètre géographique couvert par l'étude | char | - | *-* | repris dans CIRCE
contexte | le contexte de réalisation, les études antérieures, les motivations à réaliser la prestation | texte | - | *-* |
e_liees | une liste d'identifiant d'études liées. Par exemple si cette étude est la suite d'une autre... | char | `[E][id],` | *-* | séparés par des virgules
objectifs | ce qu'on attend de l'étude, les résultats attendus | texte | - | *commentaires* |
commentaire | éléments d'information n'entrant pas dans les autres champs | texte | - | *-* | par exemple historique du dossier, commentaire sur la méthode...
prospective | questions prospectives qui peuvent être associées à l'étude | texte| - | *-* |
|| __*Pilotage*__
pil_service | l'identifiant du **service** en charge du pilotage de l'étude | char | `[S][id]` | *pilotage_ddtm* |  repris dans CIRCE, permet de renseigner la fiche pour les champs *maitre d'ouvrage*, *mail*, *téléphone*,... cf. [S]
pil_contact | chef de projet : le nom du contact dans le service en charge du pilotage de l'étude | char | - | *-* | repris dans CIRCE
partenaires | d'autres services ou organisations associées au pilotage de l'étude (au sein de comités techniques ou de pilotage par exemple) | char | - | *-* |  repris dans CIRCE
m_oeuvre | le nom du prestataire qui réalise la prestation. Peut-être un bureau d'étude, le CEREMA, ou le service en régie | char | - | *nom_bureau_etude*|  repris dans CIRCE
ref_moeuvre | la référence de l'opération pour le maître d'oeuvre | char | - | *ref_devis* | n° d'opération dans Sinppa, n° de devis...
|| __*Méthodologie*__
methodologie | la méthodologie envisagée / utilisée. Les différentes étapes de réalisation. | texte | - | *-* |
ressources | les ressources à mobiliser en interne (bases de données, géomatique, équipe projet) | texte | - | *-* | prévoir une liste type ou plutôt un guide de rédaction ?|
biblio | une liste de références bibliographiques | texte | - | *-* | une bonne habitude à prendre
|| __*Programmation*__
priorité | un niveau de priorité pour la prestation | int | - | *-* | (par exemple de 0 - indispensable (liste du directeur) à 4 - pas prêt (barême à définir)
type_m_oeuvre | le type de maitrise d'oeuvre | char | {mOeuvres} | *ligne_budgetaire_lb* | (régie, CEREMA, BE, assocation...)
d_debut | la date de démarrage de la prestation (éventuellement prévisionnelle) | timestamp | - | *date_engagement_lb, date_debut_etude* |
d_fin | la date d'achèvement (éventuellement prévisionnelle) | timestamp | - | *date_fin_etude, date_facturation_lb, * |
ae_demande | estimation du montant de la prestation, demande au dialogue de gestion | numeric(8,2) | - | *montant_demande_lb* |
ae_ligne | ligne budgétaire d'imputation | char | `[B][id]` | *ligne_budgetaire_lb2* | en cohérence avec [E][themes]
ae_programme | montant retenu à la programmation à l'issue de la préprogrammation CEREMA, du dialogue de gestion, du comité des études... | numeric(8,2) | - | *montant_autorisation_lb* | 
cofinanceurs | liste des partenaires qui cofinancent l'étude ou la prestation | char | - | *-* |
ae_part | montant total des cofinancements | numeric(8,2) | - | *-* |
|| __*Suivi*__
ae_engage | montant commandé au prestataire | numeric(8,2) | - | *montant_engagement_lb* |
cp_mandate | montant facturé et réglé au prestataire | numeric(8,2 | -| *montant_facturation_lb* | 
avancement | un niveau d'avancement de l'étude en % | int | `default:0` | *pourcentage_avancement_etude* |
abandon | indique si le travail a été abandonné | bool | `default:false` | *abandon* |
||__*Valorisation*__
resume | une présentation synthétique de l'étude | texte | - | *-* | repris dans CIRCE
public | la cible principale de l'étude et des productions (services, élus, partenaires, grand-public) | char | - | *-* | produire une liste type?
valorisation | les différentes formes de valorisation mises en oeuvre (ou envisagées). Les présentations faites de l'étude, la mise en ligne sur internet / intranet, les références dans des publications... | texte | - | *valorisation_comment* |
url | un site web où l'on peut consulter les résultats de l'étude sous forme numérique| url | - | *valorisation_url*|le plus souvent sur le site de la DREAL ou de la DDTM - plus rarement intranet). Dans certains cas particulier, une adresse réseau peut convenir.
circe | identifiant CIRCE de l'étude une fois celle-ci référencée | char | - | *-*- | fourni par CIRCE

## Services [S]
Les services susceptibles de piloter les études

| champ | description | type | valeurs | ant. | commentaire |
| ----- | ----------- | :--: | :-----: | :--: | ----------- |
id | identifiant du service | char | | | par exemple l'acronyme précédé du n° de département?
nom | en général l'acronyme du service | char | - | *-* |  repris dans CIRCE
direction | la direction de rattachement du service | char | `{Directions}` | *-* | repris dans CIRCE 
mail | le mail du **service** en charge du pilotage de l'étude | mail | - | *-* | repris dans CIRCE
tel | le téléphone du **service** en charge du pilotage de l'étude | char  |`+33 0 00 00 00 00`| *-* |  repris dans CIRCE
adresse | l'adresse postale du service en charge du pilotage | char | - | *-* |  repris dans CIRCE
web | l'adresse internet du service en charge du pilotage | url | `http(s)://*` | *-* | repris dans CIRCE

## Utilisateurs [U]
Les utilisateurs de la base (correspond à l'ancienne base [R])

*Nb : il faudrait peut-être préférer un système classique admin / gest / utilisateur avec un champ role*

| champ | description | type | valeurs | ant. | commentaire |
| ----- | ----------- | :--: | :-----: | :--: | ----------- |
id | identifiant de l'utilisateur | char | - | *code_suivi* |
nom | nom complet de l'utilisateur | char | `Prénom Nom` | *username* |
mdp | le mot de passe de l'utilisateur | char | - | *mdp* |
direction | la direction d'appartenance | char | `{Directions}` | *pilotage_ddtm* |si renseigné, a accès à toutes les études de cette direction. Le mot-clé __*tout*__ ouvre un droit sur toutes les études
services | les services modifiables | char | `[S][id],` | *pilotage_ddtm* | donne accès à toutes les études pour lesquelles [E][pil_service] correspond
bop | les bop modifiables | char | `[B][bop]` | *-* | donne accès à toutes les études pour lesquelles [B][bop]\([E][ae_ligne]) et à toutes les dotations pour lesquelles [D][bop] correspondent. Le mot-clé *tout* ouvre un droit sur tous les bop

## Budgets [B]
Les lignes budgétaires de rattachement des études. Voir la [liste des BOP études](liste-bop-etudes.md) pour le recensement en cours. Une table plutôt fixe normalement, uniquement destinée à la saisie. Pour les restitutions l'outil s'appuie sur la table [E]

| champ | description | type | valeurs | ant. | commentaire |
| ----- | ----------- | :--: | :-----: | :--: | ----------- |
id | la ligne budgétaire niveau bop-action-sousAction | char | `{Budget-Bop-Lignes}` | *-* | par exemple *135-07-01f*, *09-11-10*
nom | intitulé de la ligne budgétaire | char | `{Budget-Bop-Lignes:libellé}` | *libelle_bop* |
bop | le BOP de rattachement | char | `{Budget-Bop}` | *action_bop* |
gestionnaire | les services gestionnaires de la ligne en question | char | `[S][id]` | *gestionnaire_bop* |

## Dotations [D]
Cette table liste les dotations affectées aux études et prestations sur les différents budgets

| champ | description | type | valeurs | ant. | commentaire |
| ----- | ----------- | :--: | :-----: | :--: | ----------- |
id | identifiant unique de la dotation | serial | `non nul` | *id_dotation* |
an_dot | année de la dotation | char | `aaaa` | *annee_dotation* | pour comparaison avec [E][an_prog]
ligne | la ligne budgétaire de rattachement | char | `[B][id]` | *sigle_bop_dotation* |pour comparaison avec [E][ae_ligne]
montant | montant de la dotation (en AE uniquement) | numeric(10,4) | - | *montant_dotation* |
commentaire |  | char | - | *commentaires_dotation* |
d_maj  | date de mise à jour | timestamp | - | *date_maj* | *automatique*

#Listes de choix pré-établies

## {Themes}
cf. [Thesaurus](thesaurus.md)

## {Types}
Les types de prestations pouvant figurer à la programmation
* 

## {Directions}
Les maîtres d'ouvrage faisant partie du comité des études
* DREAL, DDTM14, DDTM27, DDTM50, DDT61, DDTM76, DDCS14, DDCS27, DDCS50, DDCS61, DDCS76, DIRM, DIRNO, DRAAF

## {mOeuvres}
Les types de maitrise d'oeuvre possibles
* Régie, Bureau d'étude, CEREMA, subvention
* 

# Pour mémoire - champs non repris de l'ancienne base

#### Etudes [E]
| champ | description | type | valeurs | commentaire
| ----- | ----------- | :--: | :-----: | ----------- 
date_demande_lb_n1  |  | timestamp | - | 
date_demande_lb_n |  | timestamp | - | 
date_autorisation_lb |  | timestamp | - | 
date_devis_lb |  | timestamp | - | 
montant_demande_lb_n1 |  | numeric(8,2) | - | 
montant_demande_lb_n |  | numeric(8,2) | - | 
montant_devis_lb |  | numeric(8,2) | - | 
date_demande_lb2_n1 |  | timestamp | - | 
date_demande_lb2_n |  | timestamp | - | 
date_autorisation_lb2 |  | timestamp | - | 
date_devis_lb2 timestamp |  | timestamp | - | 
date_engagement_lb2 |  | timestamp | - | 
date_facturation_lb2 |  | timestamp | - | 
montant_demande_lb2_n1 |  | numeric(8,2) | - | 
montant_demande_lb2_n_bak |  | numeric(8,2) | - | 
montant_autorisation_lb2 |  | numeric(8,2) | - | 
montant_devis_lb2 |  | numeric(8,2) | - | 
montant_engagement_lb2 |  | numeric(8,2) | - | 
montant_facturation_lb2 |  | numeric(8,2) | - | 
date_demande_devis |  | timestamp | - | 
date_reception_devis |  | timestamp | - | 
date_verification_devis |  | timestamp | - | 
date_notification_devis |  | timestamp | - | 
date_demande_facture |  | timestamp | - | 
date_reception_facture |  | timestamp | - | 
date_transmission_facture |  | timestamp | - | 
date_acquittee_facture |  | timestamp | - | 
montant_demande_lb2 |  | numeric(8,2) | - | 
date_demande_lb timestamp |  | timestamp | - | 
date_demande_lb2 timestamp |  | timestamp | - | 
montant_demande_lb2_n1 |  | numeric(8,2) | - | 
montant_demande_lb2_n |  | numeric(8,2) | - | 
departement_cete |  | char | - | 
contact_bureau_etude |  | char | - | 

## Droits [R] > [U]
 champ | description | type | valeurs | commentaire
 ----- | ----------- | :--: |  :--:   | -----------
service |  | char | - | 
uid |  | char | - | 

## Table Budgets [B]
 champ | description | type | valeurs | commentaire
 ----- | ----------- | :--: |  :--:   | -----------
sigle_bop |  | char | - | 
commentaires_bop |  | char | - | 

## Table Dotations [D]
 champ | description | type | valeurs | commentaire
 ----- | ----------- | :--: |  :--:   | -----------
titre_dotation |  | char | - | 