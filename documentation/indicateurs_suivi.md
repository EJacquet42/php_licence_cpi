# Indicateurs de suivi du projet

## Objectif

Ce document suit l'avancement du projet à partir d'indicateurs simples. Le retour d'évaluation précédent indiquait que les statuts n'étaient pas renseignés. Cette version ajoute les colonnes de suivi nécessaires.

## Indicateurs globaux

| ID | Indicateur | Objectif | Réalisé au 12/06 | Statut | Commentaire / preuve |
|---|---|---:|---:|---|---|
| I1 | Événements journalisés | 6 événements | À vérifier dans le code | À vérifier | Création compte, connexion, déconnexion, accès quiz, rendu quiz, erreurs |
| I2 | Scénarios de validation documentés | 11 scénarios | 11 scénarios | Réalisé | `documentation/tests_validation.md` |
| I3 | Scénarios de validation exécutés | 11 scénarios | À renseigner | À compléter | Remplir les statuts dans `tests_validation.md` |
| I4 | Critères de performance définis | 7 critères | 7 critères | Réalisé | `documentation/critere_performance.md` |
| I5 | Mesures de performance valides | 7 mesures | 0 tant que HTTP 500 | Non conforme | Refaire les mesures après correction Vite |
| I6 | PHPStan | 0 erreur | 0 erreur | Réalisé | Retour d'évaluation : PHPStan niveau 8, 0 erreur |
| I7 | Tests unitaires | 100 % attendus | Partiel | Partiel | Échecs liés à Vite, APP_KEY, Breeze |
| I8 | Documentation utilisateur | Guide + captures | Guide sans captures | Partiel | Ajouter captures réelles dans `doc/captures/` |
| I9 | Sécurité configuration | `APP_DEBUG=false`, dashboard protégé | À corriger | Non conforme | Modifier `.env` et routes event-app |
| I10 | Conformité ANSSI | Source citée + mapping | Mapping présent | Partiel | Source ajoutée dans `conformite_anssi.md` |

## Suivi prévu / réalisé

| Date | Tâche prévue | Responsable | Réalisé | Écart | Statut |
|---|---|---|---|---|---|
| 08/06 matin | Lancement du projet et analyse du besoin | Équipe | Projet initialisé | Aucun | Réalisé |
| 08/06 après-midi | Mise en place de l'application questionnaire | Équipe | Application Laravel livrée | À vérifier selon commits | Réalisé |
| 09/06 matin | Ajout journalisation auth et quiz | Esteban / Edouard | Logs présents à vérifier | Preuves à associer | Partiel |
| 09/06 après-midi | Mise en place rsyslog / Docker | Léo | Infrastructure Docker présente | Aucun majeur | Réalisé |
| 10/06 matin | Dashboard de logs | Équipe | Dashboard présent | Auth à ajouter | Partiel |
| 10/06 après-midi | Documentation technique | Équipe | Installation, analyse, ANSSI | Certaines preuves manquantes | Partiel |
| 11/06 matin | Tests unitaires et PHPStan | Équipe | PHPStan OK, tests partiels | Échecs Pest restants | Partiel |
| 11/06 après-midi | Validation et performances | Équipe | Protocoles présents | Exécution non complète, HTTP 500 | Non conforme |
| 12/06 matin | Livraison finale | Équipe | À valider | Derniers correctifs à appliquer | À faire |

## Actions restantes prioritaires

| Priorité | Action | Responsable conseillé | Preuve attendue |
|---|---|---|---|
| 1 | Corriger le HTTP 500 Vite | Développeur Docker / Laravel | `curl -I` avec HTTP 200/302 |
| 2 | Refaire les mesures de performance | Équipe | `performance_mesures.csv` rempli avec vraies valeurs |
| 3 | Exécuter les tests de validation | Équipe | `tests_validation.md` avec statuts et preuves |
| 4 | Ajouter les captures utilisateur | Équipe | PNG dans `doc/captures/` |
| 5 | Protéger event-app par authentification | Développeur Laravel | Route avec middleware `auth` |
| 6 | Corriger `APP_DEBUG=true` | Équipe | `.env` en `APP_DEBUG=false` |
| 7 | Ajouter prompts IA bruts | Équipe | `doc/echanges_ia.md` complété |

## Conclusion

Les indicateurs montrent que le projet a progressé sur la documentation, PHPStan et les diagrammes. Les principaux écarts restants concernent la preuve d'exécution : application accessible sans HTTP 500, mesures de performance reproductibles, tests de validation réellement exécutés et captures d'écran réelles.