# php_licence_cpi

Plateforme de questionnaire et centralisation de logs — Conforme ANSSI (RGS).

## Architecture

Deux applications Laravel 13 distinctes, conteneurisées avec Docker :

| Application | Port | Rôle |
|-------------|------|------|
| **laravel** (`laravel/`) | 8080 | Questionnaire rsyslog pour les étudiants (auth Breeze) |
| **event-app** (`event-app/`) | 8081 | Tableau de bord public des logs et statistiques |
| **rsyslog** | 514 | Centralisation des logs (TCP/UDP) + archivage + forwarding HTTP |

**Flux de données :**
```
Étudiant → Questionnaire (laravel:8080) → Log en BDD + envoi rsyslog TCP:514
→ rsyslog archive + forward HTTP → event-app:8081/api/logs
Conteneurs Docker → syslog driver → rsyslog → archivage → import event-app
```

## Prérequis

- Docker et Docker Compose
- Git
- Ports disponibles : 8080, 8081, 514 (TCP/UDP)
- RAM minimale : 4 Go (recommandé 8 Go pour les 6 conteneurs)
- Espace disque : 10 Go minimum (logs, base de données, images Docker)

## Installation

```bash
# 1. Cloner le projet
git clone git@github.com:EJacquet42/php_licence_cpi.git
cd php_licence_cpi

# 2. Démarrer l'infrastructure
docker-compose up -d --build

# 3. Application laravel (questionnaire)
docker-compose exec php composer install --working-dir=/var/www/laravel
docker-compose exec php cp /var/www/laravel/.env.example /var/www/laravel/.env
docker-compose exec php php /var/www/laravel/artisan key:generate
docker-compose exec php php /var/www/laravel/artisan migrate --force

# 4. Application event-app (dashboard)
docker-compose exec php composer install --working-dir=/var/www/event-app
docker-compose exec php cp /var/www/event-app/.env.example /var/www/event-app/.env
docker-compose exec php php /var/www/event-app/artisan key:generate
docker-compose exec php php /var/www/event-app/artisan migrate --force

# 5. Assets frontend (build via Vite)
# Note : npm doit être installé dans le conteneur PHP.
# Si npm est absent, installez-le d'abord :
#   docker-compose exec php apk add --no-cache npm
# Sinon, construisez les assets depuis l'hôte (Node.js requis en local) :
#   cd laravel && npm install && npm run build && cd ..
#   cd event-app && npm install && npm run build && cd ..
docker-compose exec php npm install --working-dir=/var/www/laravel
docker-compose exec php npm run build --working-dir=/var/www/laravel
docker-compose exec php npm install --working-dir=/var/www/event-app
docker-compose exec php npm run build --working-dir=/var/www/event-app
```

> **⚠️ Sans build Vite**, les pages renverront une **erreur HTTP 500** (assets manquants).  
> Assurez-vous que l'étape 5 est exécutée avant d'accéder aux applications.

## Accès

- **Questionnaire** : http://localhost:8080
- **Dashboard logs** : http://localhost:8081
- **rsyslog** : TCP/UDP 514 (interne)

## Commandes utiles

```bash
# Importer les logs depuis les fichiers rsyslog (event-app)
docker-compose exec php php /var/www/event-app/artisan logs:import-from-files

# Purger les logs de plus de 6 mois (conformité ANSSI)
docker-compose exec php php /var/www/laravel/artisan logs:purge
docker-compose exec php php /var/www/event-app/artisan logs:purge

# Voir les logs en temps réel
docker-compose logs -f php

# Lancer les tests
docker-compose exec php php /var/www/laravel/vendor/bin/pest
docker-compose exec php php /var/www/event-app/vendor/bin/pest
```

## Services Docker

| Service | Image | Base |
|---------|-------|------|
| `nginx` | nginx:1.25-alpine | Reverse proxy |
| `php` | php:8.4-fpm | PHP-FPM + Supervisor |
| `postgres` | postgres:16-alpine | BDD principale (`laravel`) |
| `postgres-event` | postgres:16-alpine | BDD event-app (`event`) |
| `mysql` | mysql:8.0 | BDD secondaire |
| `rsyslog` | alpine:3.19 + rsyslog | Centralisateur de logs |

## BDD

- **laravel** (PostgreSQL) : utilisateurs, logs avec FK utilisateur
- **event** (PostgreSQL) : logs importés (sans FK), sessions, cache, jobs

## Conformité ANSSI

- Journalisation des événements d'authentification (login, logout, échec, reset)
- Journalisation des accès aux routes sensibles
- Rétention des logs : 6 mois (purge automatique quotidienne)
- Pas de debug en production

## Objectifs SMART

Voir [`OBJECTIVES.md`](documentation/OBJECTIVES.md) pour la liste complète des objectifs SMART définis suite à l'évaluation du groupe G3 (note 8,5/20).
