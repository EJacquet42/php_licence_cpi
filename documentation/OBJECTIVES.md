# Objectifs SMART — Projet de journalisation et dashboard

Définis pour le projet d'intégration d'un système de logs et d'un dashboard de visualisation,
conformément au besoin exprimé dans `documentation/context_client.md`.

---

## O1 — Couverture des événements journalisés

| Élément | Description |
|---|---|
| **S**pécifique | Journaliser 100 % des événements d'authentification (connexion, déconnexion, création de compte, échec, reset de mot de passe) et de soumission de quiz. |
| **M**esurable | Chaque événement listé dans le code est tracé en base de données avec horodatage, type d'action, utilisateur concerné et message descriptif. Vérification par requête SQL directe ou endpoint API. |
| **A**tteignable | Middleware Laravel existant (`LogAdminMiddleware`) à étendre pour couvrir tous les événements. 3 événements déjà tracés, 2 à ajouter. |
| **R**éaliste | 5 événements à tracer dans une application Laravel existante, faisable en 1 journée de développement. |
| **T**emporel | J+1 — livré pour le 12 juin 2026. |

---

## O2 — Rétention des logs conforme ANSSI

| Élément | Description |
|---|---|
| **S**pécifique | Conserver les logs pendant 6 mois minimum, avec purge automatique des logs plus anciens, conformément à la recommandation ANSSI R25. |
| **M**esurable | Commande `logs:purge` exécutée quotidiennement via Supervisor ; tout log antérieur à 6 mois est supprimé des deux bases (laravel et event-app). Vérification par inspection du cron/Supervisor et test d'insertion d'un log daté de J-181. |
| **A**tteignable | Commande `PurgeOldLogs` déjà implémentée dans les deux applications. Reste à activer la planification via Supervisor. |
| **R**éaliste | Une commande artisan existante + un fichier de configuration Supervisor. |
| **T**emporel | J+1 — livré pour le 12 juin 2026. |

---

## O3 — Centralisation des logs via rsyslog

| Élément | Description |
|---|---|
| **S**pécifique | Centraliser l'ensemble des logs applicatifs (Laravel, event-app) et des conteneurs Docker vers le conteneur rsyslog dédié, avec archivage et forwarding HTTP vers event-app. |
| **M**esurable | Tout log émis par une application est reçu sur rsyslog (TCP 514), archivé dans `/var/log/rsyslog/`, et forwardé vers `event-app:8081/api/logs`. Vérification par injection d'un log test et lecture du fichier d'archive + requête API dashboard. |
| **A**tteignable | Infrastructure Docker déjà en place (6 conteneurs, réseau dédié). Configuration rsyslog partiellement existante. |
| **R**éaliste | 1 fichier `rsyslog.conf` à compléter + driver syslog Docker à configurer sur chaque conteneur. |
| **T**emporel | J+1 — livré pour le 12 juin 2026. |

---

## O4 — Dashboard de visualisation des logs

| Élément | Description |
|---|---|
| **S**pécifique | Mettre à disposition sur `event-app:8081` un tableau de bord public listant les logs avec filtres par type d'événement, utilisateur et période, avec indicateur du nombre total de logs collectés. |
| **M**esurable | Dashboard accessible sur `/dashboard` avec : tableau paginé, filtres fonctionnels, compteur affiché. Temps de chargement < 3 secondes pour 10 000 logs. |
| **A**tteignable | Contrôleur `DashboardController` existant dans event-app ; reste à ajouter les filtres et le compteur. |
| **R**éaliste | 1 vue Blade + 1 requête Eloquent paramétrée, faisable en 2 jours. |
| **T**emporel | J+1 — livré pour le 12 juin 2026. |

---

## O5 — Conformité aux recommandations ANSSI

| Élément | Description |
|---|---|
| **S**pécifique | Implémenter et documenter les 9 recommandations ANSSI identifiées (R2, R3, R4, R6, R9, R15, R25, R26, R27) avec justification de chaque mesure prise ou arbitrage pour les non applicables. |
| **M**esurable | Document `documentation/conformite_anssi.md` listant chaque recommandation, son statut (appliquée / non applicable avec justification), et la preuve de son implémentation dans le code ou la configuration. |
| **A**tteignable | 3 recommandations déjà vérifiées dans le code (R9 centralisation, R25 rétention, logging auth) ; reste 6 à documenter et/ou implémenter. |
| **R**éaliste | 6 recommandations à traiter, 1 à 2 jours de travail. |
| **T**emporel | J+1 — livré pour le 12 juin 2026. |

---

## O6 — Qualité et structure des logs

| Élément | Description |
|---|---|
| **S**pécifique | Chaque log doit contenir un horodatage ISO 8601, un type d'action normalisé (ex. `auth.login`, `auth.logout`, `quiz.submit`), un identifiant utilisateur, un message clair en français, et le niveau de gravité (INFO, WARN, ERROR). |
| **M**esurable | Requête sur la table `logs` des deux bases : 100 % des lignes ont les 5 champs renseignés non nuls et au format attendu. Vérification par script de validation automatisé. |
| **A**tteignable | Migration à ajouter pour normaliser les champs, modification du modèle `Log` existant. |
| **R**éaliste | 1 migration + 1 modèle à modifier, faisable en 1 journée. |
| **T**emporel | J+1 — livré pour le 12 juin 2026. |

---

## O7 — Disponibilité et résilience de l'infrastructure

| Élément | Description |
|---|---|
| **S**pécifique | Assurer que l'infrastructure Docker redémarre automatiquement après un crash (restart policy) et que les logs ne sont pas perdus en cas de coupure (persistance via volumes Docker). |
| **M**esurable | `docker-compose ps` → tous les services en `Up` ; `restart: unless-stopped` sur chaque service ; volumes Docker montés pour PostgreSQL, rsyslog. Test : `docker-compose stop postgres && docker-compose start postgres` → données conservées. |
| **A**tteignable | `restart:` policy à ajouter dans `docker-compose.yml`, volumes déjà partiellement configurés. |
| **R**éaliste | Modification de 1 fichier YAML, vérification en 30 minutes. |
| **T**emporel | J+1 — livré pour le 12 juin 2026. |

---
