# Application Dashboard (event-app)

Application Laravel 13 publique — port 8081.

Tableau de bord centralisé des logs et statistiques, destiné à l'évaluateur.

## Fonctionnalités

- Dashboard public (sans authentification)
- Tableau des logs avec filtres (type, priorité, date)
- Cartes de statistiques (total logs, quiz, connexions, échecs)
- Graphiques de répartition (priorité, type)
- Graphique d'évolution des logs (7 derniers jours)
- Score moyen aux quiz
- Statistiques par question (`/questions-stats`)
- Import automatique des fichiers rsyslog

## Routes principales

| Méthode | URI | Description |
|---------|-----|-------------|
| GET | `/` | Redirection vers `/event` |
| GET | `/event` | Dashboard des logs + statistiques |
| GET | `/questions-stats` | Statistiques par question |
| POST | `/api/logs` | Réception des logs (interne) |

## Installation

```bash
docker-compose exec php composer install --working-dir=/var/www/event-app
docker-compose exec php cp /var/www/event-app/.env.example /var/www/event-app/.env
docker-compose exec php php /var/www/event-app/artisan key:generate
docker-compose exec php php /var/www/event-app/artisan migrate --force
```

## Import des logs

L'import depuis les fichiers rsyslog s'exécute automatiquement toutes les 60 secondes via Supervisor. Commande manuelle :

```bash
docker-compose exec php php /var/www/event-app/artisan logs:import-from-files
```

## Tests

```bash
docker-compose exec php php /var/www/event-app/vendor/bin/pest
```
