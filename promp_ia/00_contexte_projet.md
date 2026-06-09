# Contexte du Projet

## Objectif
Système de gestion de licences PHP — "php_licence_cpi"

## Structure actuelle
- **laravel/** — Backend Laravel (vide, squelette)
- **login-app/** — App Laravel séparée avec auth complète (Breeze)
- **docker/** — Services : nginx, php, rsyslog, postgres, mysql

## Stack technique
- PHP / Laravel
- PostgreSQL + MySQL
- Docker (nginx, php-fpm, rsyslog pour logging centralisé)
- login-app utilise Laravel Breeze pour l'authentification
