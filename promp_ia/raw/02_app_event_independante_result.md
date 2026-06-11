# Réponse — Application dashboard des logs indépendante

## Date
09/06/2026

## Réponse brute

```
Voici les réponses à vos questions :

## 1. Duplication du code
Oui, créez une nouvelle app Laravel en copiant laravel/ → event-app/.
C'est plus simple et plus fiable que de partager le même code.
Vous mutualiserez plus tard si nécessaire.

## 2. BDD dédiée
- Nouveau service postgres-event dans docker-compose
- BDD "event" avec utilisateur "event"
- Table logs avec les mêmes champs que l'app principale
- Commande artisan pour importer les logs depuis rsyslog

## 3. Parse des fichiers rsyslog
- Volume Docker partagé : rsyslog-logs monté dans php
- Commande Artisan : scanne /var/log/remote/
- Parse les fichiers JSON des logs rsyslog
- Import dans la BDD event

## 4. Architecture
```
[Questionnaire] → BDD laravel → rsyslog → fichiers log
                                                    ↓
                                          [event-app] parse
                                                    ↓
                                          BDD event → /event
```

## Décisions
- App : Nouvelle app Laravel (code dupliqué) ✓
- Port : 8081 ✓
- Stack : Laravel + Breeze + PostgreSQL ✓
- BDD : Nouveau service postgres-event ✓
- Alimentation : Parse fichiers rsyslog → import BDD ✓
- Auth : Publique (pas de login) ✓
```
