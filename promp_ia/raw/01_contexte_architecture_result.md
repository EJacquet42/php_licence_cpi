# Réponse — Contexte et architecture du projet

## Date
09/06/2026

## Réponse brute

```
Voici une architecture proposée pour votre projet :

## Structure proposée

### Routes
- GET/POST /register — Inscription
- GET/POST /login — Connexion
- GET /dashboard — Questionnaire rsyslog
- POST /dashboard/submit — Soumission du questionnaire
- GET /event — Visualisation des logs
- GET /generator — Générateur manuel de logs
- POST /generator — Envoi d'un log manuel
- POST /api/logs — Réception des logs depuis rsyslog (interne)

### Schéma BDD (table logs)
- id (bigint, PK)
- user_id (FK users)
- type (enum: quiz, manual)
- facility (string)
- priority (string)
- message (text)
- questions_data (json, nullable)
- score (int, nullable)
- total (int, nullable)
- created_at (timestamp)

### Flux des logs
1. Utilisateur soumet le questionnaire → Laravel stocke en BDD
2. Laravel envoie à rsyslog via syslog driver
3. rsyslog reçoit et stocke dans /var/log/remote/
4. rsyslog push vers API Laravel via omhttp
5. /event affiche les logs depuis la BDD

### Pages
- /dashboard : Questionnaire avec questions rsyslog
- /event : Tableau des logs avec auto-refresh 10s
- /generator : Formulaire manuel facility/priority/message

### Authentification
- Multi-utilisateurs (Breeze déjà en place)
- Chaque utilisateur voit ses propres logs

### Docker
- Conserver le réseau existant 172.22.0.0/16
- IPs statiques pour chaque service
- Volumes pour persistance
```
