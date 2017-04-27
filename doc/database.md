# Structure de la base de données
* version initiale de la base de données, la version courante peut-être intermédiaire entre celle-ci et la [nouvelle structure](base-de-donnees.md)

## Table Etudes [E]
Cette table présente les études et prestations devant faire l'objet d'une programmation.
*Pour plus de clarté dans la base, il serait sans doute utile de renommer un certain nombre de champs, de faire des regroupement par sous-parties de la description de l'étude et de mieux assurer le lien avec la fiche étude.*

 champ | description | type | valeurs | commentaire
 ----- | ----------- | :--: |  :--:   | -----------
id_etude_hn | identifiant unique de l'étude | char | - | *renommer en "id"?*
dreal | ? | char | - | *utilité du champ?*
bop | identifiant du Bop | char | `[B][action_bop]` | *à placer dans la partie "programmation"*
service | représentant de l'étude au comité des études | char | - | *utilité du champ?*
pilotage_ddtm | service pilote de l'étude | char | - | *renommer en "pilote"?*
libelle | titre de l'étude ou de la prestation | char | - | *renommer en "titre"?*
commentaires | commentaire sur l'étude | char | - | *champ à décomposer à partir de la fiche étude : objectifs, méthode...*
date_maj |  | timestamp | par défaut ('now'::text)::date |
ligne_budgetaire_lb |  | char | - |
date_demande_lb_n1  |  | timestamp | - |
date_demande_lb_n |  | timestamp | - |
date_autorisation_lb |  | timestamp | - |
date_devis_lb |  | timestamp | - |
date_engagement_lb |  | timestamp | - |
date_facturation_lb |  | timestamp | - |
montant_demande_lb_n1 |  | numeric(8,2) | - |
montant_demande_lb_n |  | numeric(8,2) | - |
montant_autorisation_lb |  | numeric(8,2) | - |
montant_devis_lb |  | numeric(8,2) | - |
montant_engagement_lb |  | numeric(8,2) | - |
montant_facturation_lb |  | numeric(8,2) | - |
ligne_budgetaire_lb2 |  | char | - |
date_demande_lb2_n1 |  | timestamp | - |
date_demande_lb2_n |  | timestamp | - |
date_autorisation_lb2 |  | timestamp | - |
date_devis_lb2 timestamp |  | timestamp | - |
date_engagement_lb2 |  | timestamp | - |
date_facturation_lb2 |  | timestamp | - |
montant_demande_lb2_n1 |  | timestamp | - |
montant_demande_lb2_n_bak |  | timestamp | - |
montant_autorisation_lb2 |  | timestamp | - |
montant_devis_lb2 |  | timestamp | - |
montant_engagement_lb2 |  | timestamp | - |
montant_facturation_lb2 |  | timestamp | - |
date_demande_devis |  | timestamp | - |
date_reception_devis |  | timestamp | - |
date_verification_devis |  | timestamp | - |
date_notification_devis |  | timestamp | - |
date_debut_etude timestamp |  | timestamp | - |
date_fin_etude |  | timestamp | - |
date_demande_facture |  | timestamp | - |
date_reception_facture |  | timestamp | - |
date_transmission_facture |  | timestamp | - |
date_acquittee_facture |  | timestamp | - |
annee_pgm |  | char | par défaut 2012 | *plutôt utiliser l'année en cours?*
pourcentage_avancement_etude |  | integer | par défaut 0 |
montant_demande_lb |  | numeric(8,2) | - |
montant_demande_lb2 |  | numeric(8,2) | - |
date_demande_lb timestamp |  | timestamp | - |
date_demande_lb2 timestamp |  | timestamp | - |
montant_demande_lb2_n1 |  | numeric(8,2) | - |
montant_demande_lb2_n |  | numeric(8,2) | - |
valorisation_comment |  | char | - |
valorisation_url |  | char | - |
departement_cete |  | char | - |
nom_bureau_etude |  | char | - |
contact_bureau_etude |  | char | - |
ref_devis |  | char | - |
abandon |  | boolean | défaut `false` |

## Table Droits [R]
Cette table liste les services en charge du pilotage des moyens d'études dans les différentes direction

 champ | description | type | valeurs | commentaire
 ----- | ----------- | :--: |  :--:   | -----------
pilotage_ddtm |  | char | - |
username |  | char | - |
code_suivi |  | char | non nul |
mdp |  | char | - |
service |  | char | - |
uid |  | char | - |

## Table Budgets [B]
Cette table liste les lignes budgétaires de rattachement des études

 champ | description | type | valeurs | commentaire
 ----- | ----------- | :--: |  :--:   | -----------
action_bop |  | char | - |
sigle_bop |  | char | - |
libelle_bop |  | char | - |
gestionnaire_bop |  | char | - |
commentaires_bop |  | char | - |

## Table Dotations [D]
Cette table liste les dotations affectées aux études et prestations sur les différents budgets

 champ | description | type | valeurs | commentaire
 ----- | ----------- | :--: |  :--:   | -----------
id_dotation |  | serial | non nul |
annee_dotation |  | char | - |
sigle_bop_dotation |  | char | - |
titre_dotation |  | char | - |
montant_dotation |  | numeric(10,4) | - |
commentaires_dotation |  | char | - |
date_maj  |  | char 'dd-mm-yyyy' | - | *pourquoi pas  timestamp?*
