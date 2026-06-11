# Gestion des risques

## Objectif

Ce document identifie les principaux risques du projet, leur criticité, les mesures prévues pour les limiter et l'état de traitement. Il complète le pilotage du projet et permet de prouver que les difficultés techniques ont été anticipées.

## Matrice de criticité

| Probabilité | Impact faible | Impact moyen | Impact fort |
|---|---|---|---|
| Faible | Faible | Faible | Moyen |
| Moyenne | Faible | Moyen | Fort |
| Forte | Moyen | Fort | Critique |

## Registre des risques

| ID | Risque | Probabilité | Impact | Criticité | Mesure préventive | Mesure corrective | Statut |
|---|---|---|---|---|---|---|---|
| R1 | Les logs applicatifs ne sont pas générés | Moyenne | Fort | Fort | Ajouter un log par événement critique et tester chaque action | Corriger le contrôleur ou le middleware concerné | À vérifier |
| R2 | Les logs ne sont pas reçus par rsyslog | Moyenne | Fort | Fort | Vérifier `rsyslog.conf`, port 514 et réseau Docker | Lire les logs du conteneur rsyslog et corriger la conf | À vérifier |
| R3 | Les logs contiennent des données sensibles | Faible | Fort | Moyen | Exclure mots de passe, tokens, secrets et clés API | Purger les logs concernés et corriger le message | À surveiller |
| R4 | Le dashboard de logs est inaccessible | Moyenne | Moyen | Moyen | Vérifier nginx, event-app et routes | Corriger la route ou le build Vite | À vérifier |
| R5 | Erreur HTTP 500 liée à Vite | Moyenne | Moyen | Moyen | Documenter `npm install` et `npm run build` | Générer les assets depuis hôte ou conteneur | Identifié |
| R6 | Les tests automatisés échouent | Moyenne | Moyen | Moyen | Lancer Pest avant livraison | Corriger les tests Breeze/Vite/email | En cours |
| R7 | PHPStan est mal configuré | Moyenne | Moyen | Moyen | Utiliser une configuration compatible PHPStan 2.x | Remplacer les options dépréciées | En cours |
| R8 | Documentation insuffisante | Forte | Fort | Critique | Ajouter installation, utilisation, validation, performance | Compléter les fichiers manquants | En cours |
| R9 | Planning jugé rétrospectif | Moyenne | Moyen | Moyen | Ajouter prévu/réalisé et écarts | Compléter le suivi dans `indicateurs_suivi.md` | En cours |
| R10 | Répartition des commits déséquilibrée | Moyenne | Moyen | Moyen | Répartir les corrections restantes | Chaque membre ajoute des commits documentaires ou tests | À surveiller |

## Risques prioritaires

Les risques à traiter en priorité avant livraison sont :

1. **R8 — Documentation insuffisante**, car plusieurs critères d'évaluation reposent sur des preuves dans le dépôt.
2. **R2 — Réception rsyslog**, car la centralisation est au cœur du projet.
3. **R5 — Erreur HTTP 500 Vite**, car elle bloque la démonstration de l'application.
4. **R6/R7 — Tests et PHPStan**, car ils impactent directement la partie vérification.

## Plan d'action

| Action | Risque concerné | Responsable | Échéance | Preuve attendue |
|---|---|---|---|---|
| Ajouter `tests/validation.md` | R8 | Esteban | 12 juin | Document de tests |
| Ajouter `documentation/performance.md` | R8 | Esteban | 12 juin | Protocole + résultats |
| Ajouter `conformite_anssi.md` | R8 | Léo | 12 juin | Mapping recommandations |
| Corriger PHPStan | R7 | Edouard | 12 juin | Sortie PHPStan |
| Générer les assets Vite | R5 | Équipe | 12 juin | Application HTTP 200 |
| Capturer réception rsyslog | R2 | Léo | 12 juin | Commande ou capture |

## Conclusion

La gestion des risques montre que les problèmes principaux du projet ont été identifiés et associés à des actions de correction. Les statuts doivent être mis à jour avec les résultats réels avant la remise finale.
