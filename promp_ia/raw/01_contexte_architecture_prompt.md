# Prompt — Contexte et architecture du projet

## Date
09/06/2026

## Prompt

```
Je travaille sur un projet PHP de gestion de licences appelé "php_licence_cpi".
Le projet doit intégrer un système de logging centralisé avec rsyslog.

Structure actuelle :
- laravel/ — Backend Laravel (squelette vide)
- login-app/ — App Laravel séparée avec Breeze (auth complète)
- docker/ — Services : nginx, php-fpm, rsyslog, postgres, mysql
- docker-compose.yml avec réseau app-network (172.22.0.0/16)

Objectif : créer une application web Laravel qui :
1. Présente un questionnaire sur rsyslog
2. Génère des logs à partir des réponses
3. Envoie les logs vers rsyslog
4. Permet de visualiser les logs générés

Peux-tu me proposer une architecture détaillée pour ce projet ?
Notamment : structure des routes, schéma de base de données,
flux des logs, et organisation du code.
```
