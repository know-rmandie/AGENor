# Rapports
Présentation des différentes mises en formes possibles à partir de la base de données.

## Fiche étude
*accès via `fiche.php?id=[E][id]`*

Une mise en forme de la [fiche étude](fiche-etude.md)

## Programme de l'année
*accès via `index.php?filtre=valeurFiltre`*

La liste des études programmées une année donnée. Cette liste peut être présentée soit sous forme thématique, soit sous forme budgétaire.
Le rapport doit permettre de filtrer facilement sur les critères suivants :
* année du programme [E][an_prog]
* statut d'avancement *calculé*
* service pilote [E][pil_service]
* type de prestation [E][type]

### Version thématique
Cette vue est la vue par défaut. Elle permet de visualiser le programme d'études pour une année en cours.
Regroupement des études par *{Themes}* cela peut conduire à des doublons, certaines études pouvant être concernées par plusieurs thèmes.

Le rapport doit permettre de filtrer facilement sur les critères suivants :
* type de prestation [E][type]

#### Lignes titres
|         |
| ------- |
| {Theme} |
#### Lignes études
| statut      | Libellé                                 | pilote           | montant      | prestataire   |
| ----------- | --------------------------------------- | ---------------- | ------------ | ------------- |
| *statut*(1) | [E][titre] - [E][id] / [E][ref_moeuvre] | [E][pil_service] | *montant*(2) | [E][m_oeuvre] |

### Version budgétaire
Cette vue est la vue qui permet de faire la programmation budgétaire.
Regroupement des études par *BOP* / *ligne budgétaire*

#### Lignes titres
On fait un regroupement par BOP puis par ligne budgétaire

|          |                                     | demandé          | programmé          | engagé          |
| -------- | ----------------------------------- | ---------------- | ------------------ | --------------- |
| [B][BOP] | `Σ[D][montant]`                      | `Σ[E][ae_demande]` | `Σ[E][ae_programme]` | `Σ[E][ae_engage]` |
|          | [B][id] - [B][nom] -  `Σ[D][montant]` | `Σ[E][ae_demande]` | `Σ[E][ae_programme]` | `Σ[E][ae_engage]` |
#### Lignes études
| statut      | Libellé                                 | pilote           | demandé         | programmé         | engagé         |
| ----------- | --------------------------------------- | ---------------- | --------------- | ----------------- | -------------- |
| *statut*(1) | [E][titre] - [E][id] / [E][ref_moeuvre] | [E][pil_service] | [E][ae_demande] | [E][ae_programme] | [E][ae_engage] |

## Etudes en cours
*à décrire dont le filtrage __programme du directeur__*

----
1. le __*statut*__ est calculé suivant les principes du flux de production
2. le __*montant*__ est le montant engagé ou à défaut, le montant de l'estimation. Pour les travaux en régie, on indique *régie*


