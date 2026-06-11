# Application Questionnaire (laravel)

Application Laravel 13 avec authentification Breeze — port 8080.

Questionnaire rsyslog de 20 questions pour les étudiants, avec génération et consultation de logs.

## Fonctionnalités

- Inscription / Connexion / Mot de passe oublié (Breeze)
- Quiz rsyslog (20 questions, score, JSON de réponses)
- Visualisation des logs personnels avec filtres (`/event`)
- Générateur manuel de logs (`/generator`)
- Édition du profil avec journalisation
- Envoi des logs vers rsyslog (TCP 514) et event-app (HTTP POST)

## Routes principales

| Méthode | URI | Middleware | Description |
|---------|-----|-----------|-------------|
| GET | `/` | - | Redirection vers `/login` |
| GET/POST | `/login` | guest | Connexion |
| GET/POST | `/register` | guest | Inscription |
| GET | `/dashboard` | auth+verified | Quiz |
| POST | `/dashboard/submit` | auth | Soumission du quiz |
| GET | `/event` | log.sensitive | Visualisation des logs |
| GET/POST | `/generator` | auth+log.sensitive | Générateur de logs |
| GET/PATCH/DELETE | `/profile` | auth+log.sensitive | Gestion du profil |

## Installation

```bash
docker-compose exec php composer install --working-dir=/var/www/laravel
docker-compose exec php cp /var/www/laravel/.env.example /var/www/laravel/.env
docker-compose exec php php /var/www/laravel/artisan key:generate
docker-compose exec php php /var/www/laravel/artisan migrate --force
```

## Tests

```bash
docker-compose exec php php /var/www/laravel/vendor/bin/pest
```
