# php_licence_cpi

Plateforme de questionnaire sur rsyslog avec centralisation et consultation des logs.

Le projet contient deux applications Laravel conteneurisées avec Docker :

| Application | Port | Rôle |
|---|---:|---|
| `laravel/` | 8080 | Questionnaire rsyslog, authentification et génération de logs |
| `event-app/` | 8081 | Dashboard public de consultation des logs et statistiques |
| `rsyslog` | 514 TCP/UDP | Centralisation, archivage et forwarding des journaux |

## Architecture générale

```text
Utilisateur -> laravel:8080 -> logs BDD + rsyslog TCP 514
Conteneurs Docker -> driver syslog -> rsyslog -> fichiers archivés
rsyslog -> HTTP POST /api/logs -> event-app:8081 -> dashboard logs
```

## Prérequis

- Docker et Docker Compose
- Git
- Node.js LTS si les assets Vite sont compilés depuis l'hôte
- Ports disponibles : 8080, 8081, 514 TCP/UDP
- RAM minimale : 4 Go, 8 Go recommandé
- Espace disque : 10 Go minimum

## Installation rapide

```bash
git clone https://github.com/EJacquet42/php_licence_cpi.git
cd php_licence_cpi
git checkout feat_doc

docker compose up -d --build
```

Installation Laravel :

```bash
docker compose exec php composer install --working-dir=/var/www/laravel
docker compose exec php cp /var/www/laravel/.env.example /var/www/laravel/.env
docker compose exec php php /var/www/laravel/artisan key:generate
docker compose exec php php /var/www/laravel/artisan migrate --force

docker compose exec php composer install --working-dir=/var/www/event-app
docker compose exec php cp /var/www/event-app/.env.example /var/www/event-app/.env
docker compose exec php php /var/www/event-app/artisan key:generate
docker compose exec php php /var/www/event-app/artisan migrate --force
```

Build des assets Vite :

```bash
cd laravel && npm install && npm run build && cd ..
cd event-app && npm install && npm run build && cd ..
```

> Sans build Vite, certaines pages peuvent retourner une erreur HTTP 500.

## Accès

| Service | URL |
|---|---|
| Questionnaire | `http://localhost:8080` |
| Dashboard logs | `http://localhost:8081/event` |
| Statistiques questions | `http://localhost:8081/questions-stats` |

## Commandes utiles

```bash
# Voir l'état des conteneurs
docker compose ps

# Voir les logs Docker
docker compose logs -f

# Voir les logs rsyslog
docker compose logs rsyslog --tail=50

# Importer les fichiers rsyslog dans event-app
docker compose exec php php /var/www/event-app/artisan logs:import-from-files

# Purger les logs de plus de 6 mois
docker compose exec php php /var/www/laravel/artisan logs:purge
docker compose exec php php /var/www/event-app/artisan logs:purge
```

## Tests et qualité

```bash
# Tests Pest
docker compose exec php php /var/www/laravel/vendor/bin/pest
docker compose exec php php /var/www/event-app/vendor/bin/pest

# PHPStan
docker compose exec php php /var/www/laravel/vendor/bin/phpstan analyse --memory-limit=512M
docker compose exec php php /var/www/event-app/vendor/bin/phpstan analyse --memory-limit=512M
```

## Documentation du projet

| Document | Rôle |
|---|---|
| `documentation/context_client.md` | Contexte, besoin, objectifs, contraintes |
| `documentation/analyse.md` | UML, déploiement, synoptique, sitemap, mockups |
| `documentation/conformite_anssi.md` | Mapping recommandations ANSSI / preuves |
| `documentation/planning.md` | Planning prévu/réalisé, responsables et jalons |
| `documentation/gestion_erreur.md` | Gestion des risques |
| `documentation/indicateurs_suivi.md` | Indicateurs de pilotage |
| `documentation/performance.md` | Protocole et résultats de performance |
| `tests/validation.md` | Tests de validation fonctionnelle |
| `doc/utilisation.md` | Guide utilisateur |
| `documentation/installation.md` | Installation et configuration serveur |

## Événements journalisés

| Événement | Type conseillé | Niveau |
|---|---|---|
| Création de compte | `auth.register` | `info` |
| Connexion réussie | `auth.login` | `info` |
| Connexion échouée | `auth.failed` | `warning` |
| Déconnexion | `auth.logout` | `info` |
| Accès route sensible | `route.sensitive` | `info` |
| Soumission quiz | `quiz.submit` | `info` ou `warning` |
| Erreur d'envoi rsyslog | `log.forward_error` | `error` |
| Purge des logs | `log.purge` | `info` |

## Sécurité et conformité

- Les mots de passe, tokens et secrets ne doivent jamais être écrits dans les logs.
- `APP_DEBUG` doit être à `false` en production.
- Les logs sont centralisés avec rsyslog.
- Une durée de conservation de 6 mois est prévue.
- Les preuves de conformité sont décrites dans `documentation/conformite_anssi.md`.