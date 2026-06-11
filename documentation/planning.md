# Planning de production du logiciel et des logs

Période : du **lundi 8 juin à 12h** au **vendredi 12 juin à 12h**.

## Objectif du planning

Ce planning permet de suivre la production du site de questionnaire rsyslog, l'intégration des logs, la centralisation avec rsyslog, la documentation et les preuves de validation. Il ne présente pas seulement les tâches prévues : il indique aussi le réalisé, le livrable attendu et les écarts.

## Planning détaillé prévu/réalisé

| Date / horaire | Tâches prévues | Livrable attendu | Responsable | Statut | Réalisé / preuve | Écart |
|---|---|---|---|---|---|---|
| Lundi 8 juin — 12h à 14h | Analyse du besoin, identification des événements à journaliser, répartition des tâches | Liste des événements à logger | Équipe | Terminé | Événements listés dans `context_client.md` | Aucun |
| Lundi 8 juin — 14h à 17h | Analyse de l'existant : routes Laravel, contrôleurs, authentification, rendu quiz | Analyse de l'existant | Esteban | Terminé | Contexte + analyse fonctionnelle | Aucun |
| Mardi 9 juin — 8h à 10h | Vérification environnement Docker et rsyslog | Conteneurs fonctionnels | Léo | Terminé | `docker-compose.yml`, service rsyslog | Preuve de commande à ajouter |
| Mardi 9 juin — 10h à 12h | Ajout logs création de compte et connexion | Logs inscription / connexion | Edouard | Terminé | Logs d'authentification | Vérifier nommage normalisé |
| Mardi 9 juin — 13h à 15h | Ajout log déconnexion | Log de déconnexion | Edouard | Terminé | Log `auth.logout` attendu | Ajouter preuve validation |
| Mardi 9 juin — 15h à 17h | Ajout logs accès questionnaire et rendu quiz | Logs questionnaire / rendu quiz | Esteban | Terminé | Log de soumission quiz | Ajouter test manuel |
| Mercredi 10 juin — 8h à 10h | Configuration transmission vers rsyslog | Logs reçus par rsyslog | Léo | Terminé partiel | Service rsyslog + port 514 | Capture réception à ajouter |
| Mercredi 10 juin — 10h à 12h | Vérification format logs : date, utilisateur, action, niveau | Format validé | Équipe | En cours | Format défini dans documentation | Contrôle SQL à ajouter |
| Mercredi 10 juin — 13h à 15h | Amélioration interface consultation logs | Dashboard event-app | Esteban | Terminé partiel | Route `/event` et filtres | Capture utilisateur à ajouter |
| Mercredi 10 juin — 15h à 17h | Tests manuels du parcours complet | Résultats des tests | Équipe | En cours | `tests/validation.md` à compléter | Résultats réels à renseigner |
| Jeudi 11 juin — 8h à 10h | Correction des bugs détectés | Application corrigée | Équipe | En cours | Tests métier ajoutés | Corriger tests échoués |
| Jeudi 11 juin — 10h à 12h | Documentation installation et utilisation | README + guide utilisateur | Edouard | En cours | `installation.md`, `utilisation.md` | Captures à ajouter |
| Jeudi 11 juin — 13h à 15h | Rédaction des preuves : captures, commandes, exemples logs | Dossier de preuves | Esteban | En cours | Validation + performance | Captures réelles à ajouter |
| Jeudi 11 juin — 15h à 17h | Finalisation risques, indicateurs, planning | Documents gestion projet | Équipe | En cours | Risques + indicateurs | Mettre à jour statuts |
| Vendredi 12 juin — 8h à 10h | Relecture, vérification dépôt Git, nettoyage | Dépôt propre | Équipe | À faire | `git status`, relecture | Dernier contrôle |
| Vendredi 12 juin — 10h à 12h | Livraison finale et vérification livrables | Version finale | Équipe | À faire | Commit final | Aucun |

## Répartition par personne

| Personne | Tâches principales | Livrables associés |
|---|---|---|
| Léo | Docker, rsyslog, centralisation, conformité ANSSI | `docker-compose.yml`, `conformite_anssi.md`, preuves rsyslog |
| Edouard | Authentification, documentation installation, PHPStan | logs auth, `installation.md`, PHPStan |
| Esteban | Questionnaire, rendu quiz, dashboard, validation et performance | logs quiz, `tests/validation.md`, `performance.md`, `analyse.md` |
| Équipe | Relecture, tests, correction, livraison | README, planning, indicateurs, risques |

## Jalons

| Jalon | Date limite | Critère de réussite | Statut |
|---|---|---|---|
| J1 — Besoin validé | 8 juin 17h | Événements à logger définis | Terminé |
| J2 — Logs applicatifs | 9 juin 17h | Inscription, connexion, déconnexion, quiz loggés | Terminé à vérifier |
| J3 — Centralisation | 10 juin 12h | Logs reçus par rsyslog | Terminé à prouver |
| J4 — Dashboard | 10 juin 17h | Logs visibles sur event-app | Terminé partiel |
| J5 — Validation | 11 juin 15h | Tests manuels renseignés | En cours |
| J6 — Livraison | 12 juin 12h | Dépôt complet, documenté et testable | À faire |

## Conclusion

Le planning montre l'organisation du travail, les responsables, les livrables et les écarts identifiés. Il doit être conservé dans le dépôt pour prouver le suivi du projet.
