# Indicateurs de suivi du projet

## Objectif

Ce document permet de prouver le pilotage du projet à l'aide d'indicateurs simples : avancement prévu/réalisé, état des livrables, suivi des tests et traitement des risques.

## Indicateurs retenus

| ID | Indicateur | Méthode de calcul | Objectif | Fréquence |
|---|---|---|---|---|
| I1 | Avancement global | Livrables terminés / livrables prévus | 100 % avant livraison | Quotidien |
| I2 | Couverture des événements journalisés | Événements loggés / événements prévus | 100 % | Quotidien |
| I3 | Validation fonctionnelle | Tests validés / tests prévus | 100 % | Fin de développement |
| I4 | Qualité du code | Tests automatisés passants + PHPStan | Aucun échec bloquant | À chaque fin de journée |
| I5 | Documentation | Documents présents / documents attendus | 100 % | Quotidien |
| I6 | Risques suivis | Risques traités / risques ouverts | 100 % des risques critiques traités | Quotidien |

## Tableau de suivi prévu/réalisé

| Date | Livrable prévu | Responsable | Statut | Réalisé | Écart / commentaire |
|---|---|---|---|---|---|
| Lundi 8 juin | Analyse de l'existant et événements à logger | Équipe | Terminé | Contexte, besoin, événements critiques | Aucun écart majeur |
| Mardi 9 juin | Logs inscription, connexion, déconnexion, quiz | Esteban / Edouard | Terminé | Logs applicatifs ajoutés | Vérifier le nommage homogène des types |
| Mercredi 10 juin | Centralisation rsyslog et dashboard | Léo / Esteban | Terminé partiel | Docker + rsyslog + event-app | Ajouter preuves de réception et captures |
| Jeudi 11 juin | Documentation, tests, preuves | Équipe | En cours | Documentation enrichie | Ajouter validation et performances |
| Vendredi 12 juin | Relecture et livraison | Équipe | À vérifier | Dépôt final | Vérifier README, tests, PHPStan |

## Suivi des livrables

| Livrable | Fichier attendu | Statut | Commentaire |
|---|---|---|---|
| Contexte et besoin | `documentation/context_client.md` | Terminé | Spécifique au projet rsyslog |
| Objectifs SMART | `documentation/OBJECTIVES.md` | Terminé | Ajouter lien vers conformité ANSSI |
| Analyse ANSSI | `documentation/conformite_anssi.md` | À ajouter | Mapping recommandation → preuve |
| Planning | `documentation/planning.md` | À améliorer | Ajouter réalisé, statut et écarts |
| Gestion des risques | `documentation/gestion_erreur.md` | À améliorer | Ajouter criticité et suivi |
| Indicateurs projet | `documentation/indicateurs_suivi.md` | À ajouter | Présent document |
| Tests validation | `tests/validation.md` | À ajouter | Use cases rejouables |
| Performance | `documentation/performance.md` | À ajouter / compléter | Protocole + résultats |
| Guide utilisateur | `doc/utilisation.md` | À ajouter | Avec captures ou emplacements de captures |
| Installation | `documentation/installation.md` | À ajouter | Procédure dédiée et vérifiable |

## Suivi des tests

| Type de test | Nombre prévu | Nombre réalisé | Statut | Commentaire |
|---|---:|---:|---|---|
| Tests manuels use cases | 11 | À renseigner | En cours | Voir `tests/validation.md` |
| Tests unitaires / feature | 48 | À renseigner | En cours | Corriger tests Breeze/Vite si nécessaire |
| PHPStan Laravel | 1 | À renseigner | En cours | Configuration PHPStan 2.x fournie |
| PHPStan event-app | 1 | À renseigner | En cours | Configuration PHPStan 2.x fournie |
| Mesures performance | 8 | À renseigner | En cours | Voir `documentation/performance.md` |

## Synthèse d'avancement

| Catégorie | Avancement estimé | Commentaire |
|---|---:|---|
| Fonctionnalités | 85 % | Les fonctions principales existent, corrections à vérifier |
| Logs et rsyslog | 80 % | Centralisation présente, preuves à compléter |
| Documentation | 70 % | Documents ajoutés, captures à intégrer |
| Validation | 40 % | Scénarios fournis, résultats à renseigner |
| Qualité code | 60 % | Tests métier présents, PHPStan à corriger |

## Conclusion

Ce suivi permet de montrer que le projet n'est pas seulement développé, mais également piloté. Les indicateurs doivent être mis à jour avec les résultats réels avant la livraison finale.
