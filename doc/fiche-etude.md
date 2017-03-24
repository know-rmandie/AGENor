# Fiche Etude
*ce document présente la structure de base d'une fiche étude. Un lien est fait ici avec les bases de données de l'outil et leurs champs pour comprendre l'interaction entre le travail réalisé par le service pilote et l'outil de suivi. Pour une utilisation en production, il est recommandé de télécharger [la version .odt](modFicheEtude.odt) de la fiche. Les références de la fiche étude notés [X][xxxx] correspondent aux champs correspondants dans la [structure de la base](base-de-donnees.md).*

*Les champs suivis d'un `C` sont réutilisés dans [CIRCE](http://www.etudes-normandie.fr/) éventuellement suivi du nom du champ correspondant si il n'est pas explicite*

## Renseignements généraux
* Identifiant : [E][id] - un identifiant unique pour l'étude
* Programme : [E][an_prog] - l'année de programmation de l'étude. Pour les idées et projets d'études, par défaut cette année est l'année suivante à la date de création de la fiche
* Titre  : [E][titre] `C` - un titre explicite pour l'étude / la prestation
* Type : [E][type] - le type de prestation (étude, AMO, ...)
* Zone Géographique : [E][zone_geo] `C` - le périmètre géographique couvert par l'étude
* Thèmes : [E][themes] `C`- la (les) thématique(s) de l'étude, sélectionnée(s) dans le thésaurus à disposition. Par ordre d'importance. Le premier thème est le thème principal de l'étude.
* Mots-Clés : [E][mots_cles] `C` - une liste de mots clés libre, complémentaires de la (des) thématique(s)
* Mise à jour : [E][d_maj] - date de la mise à jour de la fiche
* Etudes liées : [E][e_liees] - une liste d'identifiant d'études liées. Par exemple si cette étude est la suite d'une autre...
* Commentaire : [E][commentaire] - éléments d'information n'entrant pas dans les autres champs (historique du dossier...)

## Description
* Contexte : [E][contexte] - le contexte de réalisation, les études antérieures, les motivations à réaliser la prestation
* Objectifs, résultats attendus : [E][objectifs] - ce qu'on attend de l'étude, les résultats attendus
* Dimension prospective : [E][prospective] - questions prospectives qui peuvent être associées à l'étude

## Pilotage
* Maitre d'ouvrage : [S][direction][[E][pil_service]] `C` -  le maitre d'ouvrage de l'étude. En général, la direction qui demande la programmation de l'étude (DREAL, DDTMxx, ...)
* Pilote
 * Contact : [S][nom][[E][pil_service]] `C` - le **service** en charge du pilotage de l'étude
 * Mail : [S][mail][[E][pil_service]] `C` - le mail du **service** en charge du pilotage de l'étude
 * Téléphone : [S][tel][[E][pil_service]] `C` - le téléphone du **service** en charge du pilotage de l'étude
 * Adresse : [S][adresse][[E][pil_service]] `C` - l'adresse postale du service en charge du pilotage
 * Site Web : [S][web][[E][pil_service]] `C` - l'adresse internet du service en charge du pilotage
 * Chef de projet : [E][pil_contact] - le nom du contact dans le service en charge du pilotage de l'étude
* Partenaires : [E][partenaires] - d'autres services ou organisations associées au pilotage de l'étude (au sein de comités techniques ou de pilotage par exemple)
* Maitre d'oeuvre : [E][m_oeuvre] `C` - le nom du prestataire qui réalise la prestation. Peut-être un bureau d'étude, le CEREMA, ou le service en régie

## Méthodologie
* Méthodologie, déroulement : [E][methodologie] - la méthodologie envisagée / utilisée. Les différentes étapes de réalisation.
* Ressources : [E][ressources] - les ressources à mobiliser en interne (bases de données, géomatique, équipe projet)
* Bibliographie : [E][biblio] - une liste de références bibliographiques

## Programmation
* Priorité : [E][priorité] - un niveau de priorité pour la prestation (par exemple de 0 - déjà parti à 3 - peut attendre éventuellement | barême à définir)
* Type de maitrise d'oeuvre : [E][type_m_oeuvre] - le type de maitrise d'oeuvre (régie, CEREMA, BE, assocation...)
* Dates clés
 * date de début : [E][d_debut] - la date de démarrage de la prestation (éventuellement prévisionnelle)
 * date de fin : [E][d_fin] - la date d'achèvement (éventuellement prévisionnelle)
* Budget
 * montant demandé : [E][ae_demande] - estimation du montant de la prestation, demande au dialogue de gestion
 * imputation : [E][ae_ligne] - ligne budgétaire d'imputation
 * montant programmé : [E][ae_programme] - montant retenu à la programmation à l'issue de la préprogrammation CEREMA, du dialogue de gestion, du comité des études...
 * partenaires financiers : [E][cofinanceurs] - liste des partenaires qui cofinancent l'étude ou la prestation
 * montant des cofinancements : [E][ae_part] - montant des cofinancements des partenaires

## Suivi
*a-t-on besoin de dates pour ce suivi. En pratique, on a constaté que ces champs étaient mal saisis. La version proposée ici est simple et et minimale*
* montant engagé : [E][ae_engage] - montant commandé au prestataire
* montant dépensé : [E][cp_mandate] - montant facturé et réglé au prestataire
* avancement : [E][avancement] - un niveau d'avancement de l'étude en %
* abandon : [E][abandon] - indique si le travail a été abandonné

## Valorisation
* Résumé : [E][resume] `C` - une présentation synthétique de l'étude
* Cible, Public : [E][public] - la cible principale de l'étude et des productions (services, élus, partenaires, grand-public)
* Valorisation : [E][valorisation] - les différentes formes de valorisation mises en oeuvre (ou envisagées). Les présentations faites de l'étude, la mise en ligne sur internet / intranet, les références dans des publications...
* url : [E][url] - un site web où l'on peut consulter les résultats de l'étude sous forme numérique (le plus souvent sur le site de la DREAL ou de la DDTM - plus rarement intranet). Dans certains cas particulier, une adresse réseau peut convenir.
* CIRCE : [E][circe] `C` - identifiant CIRCE de l'étude une fois celle-ci référencée
