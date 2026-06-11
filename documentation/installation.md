# Procédure d'installation et de configuration serveur

## Objectif

Ce document décrit l'installation complète du projet `php_licence_cpi` dans un environnement Docker. Il complète le README avec une procédure dédiée, vérifiable et adaptée à une évaluation.

## Prérequis

| Élément | Version conseillée |
|---|---|
| Docker | Version récente avec Docker Compose |
| Git | Dernière version stable |
| Node.js | Version LTS si build Vite depuis l'hôte |
| RAM | 4 Go minimum, 8 Go recommandé |
| Disque | 10 Go minimum |
| Ports libres | 8080, 8081, 514 TCP/UDP |

## Récupération du projet

```bash
git clone https://github.com/EJacquet42/php_licence_cpi.git
cd php_licence_cpi
git checkout feat_doc
```

## Démarrage de l'infrastructure

```bash
docker compose up -d --build
```

Vérifier les conteneurs :

```bash
docker compose ps
```

Les services attendus sont :

| Service | Rôle |
|---|---|
| `nginx` | Reverse proxy pour les deux applications |
| `php` | Exécution PHP-FPM et commandes Laravel |
| `postgres` | Base de données de l'application questionnaire |
| `postgres-event` | Base de données du dashboard logs |
| `mysql` | Base secondaire si nécessaire |
| `rsyslog` | Centralisation des logs |

## Installation Laravel — Application questionnaire

```bash
docker compose exec php composer install --working-dir=/var/www/laravel

docker compose exec php cp /var/www/laravel/.env.example /var/www/laravel/.env

docker compose exec php php /var/www/laravel/artisan key:generate

docker compose exec php php /var/www/laravel/artisan migrate --force
```

## Installation Laravel — Application event-app

```bash
docker compose exec php composer install --working-dir=/var/www/event-app

docker compose exec php cp /var/www/event-app/.env.example /var/www/event-app/.env

docker compose exec php php /var/www/event-app/artisan key:generate

docker compose exec php php /var/www/event-app/artisan migrate --force
```

## Compilation des assets Vite

Si `npm` est disponible dans le conteneur PHP :

```bash
docker compose exec php npm install --working-dir=/var/www/laravel
docker compose exec php npm run build --working-dir=/var/www/laravel

docker compose exec php npm install --working-dir=/var/www/event-app
docker compose exec php npm run build --working-dir=/var/www/event-app
```

Si `npm` n'est pas disponible dans le conteneur, compiler depuis l'hôte :

```bash
cd laravel
npm install
npm run build
cd ..

cd event-app
npm install
npm run build
cd ..
```

> Sans build Vite, certaines pages peuvent retourner une erreur HTTP 500 parce que le manifeste des assets est absent.

## Accès aux applications

| Application | URL |
|---|---|
| Questionnaire | `http://localhost:8080` |
| Dashboard logs | `http://localhost:8081` |
| rsyslog | TCP/UDP 514, interne Docker |

## Vérifications après installation

### Vérifier les pages HTTP

```bash
curl -I http://localhost:8080/login
curl -I http://localhost:8081/event
```

### Vérifier rsyslog

```bash
docker compose logs rsyslog --tail=50
docker compose exec rsyslog ls -R /var/log/remote
```

### Vérifier les migrations

```bash
docker compose exec php php /var/www/laravel/artisan migrate:status
docker compose exec php php /var/www/event-app/artisan migrate:status
```

## Tests et qualité

### Tests Pest

```bash
docker compose exec php php /var/www/laravel/vendor/bin/pest
docker compose exec php php /var/www/event-app/vendor/bin/pest
```

### PHPStan

```bash
docker compose exec php php /var/www/laravel/vendor/bin/phpstan analyse --memory-limit=512M
docker compose exec php php /var/www/event-app/vendor/bin/phpstan analyse --memory-limit=512M
```

## Problèmes fréquents

| Problème | Cause possible | Correction |
|---|---|---|
| HTTP 500 sur Laravel | Assets Vite non compilés | Exécuter `npm install && npm run build` |
| Page event-app inaccessible | nginx ou event-app mal démarré | `docker compose logs nginx php` |
| Aucun log rsyslog | Port 514 ou configuration syslog | Vérifier `docker-compose.yml` et `rsyslog.conf` |
| Migrations échouées | Base non prête | Relancer après quelques secondes |
| PHPStan échoue sur configuration | Options dépréciées | Utiliser la configuration PHPStan 2.x fournie |

## Conclusion

Cette procédure permet d'installer, démarrer et vérifier l'ensemble du projet. Elle doit être suivie avant la démonstration ou l'évaluation afin de s'assurer que les deux applications et rsyslog sont opérationnels.
