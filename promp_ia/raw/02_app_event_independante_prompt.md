# Prompt — Application dashboard des logs indépendante

## Date
09/06/2026

## Prompt

```
J'ai besoin de créer une application séparée pour le dashboard de logs.

Contexte actuel :
- L'application questionnaire est sur laravel/ (port 8080)
- Le dashboard des logs est actuellement sur /event dans la même app
- On veut séparer le dashboard dans sa propre application pour l'indépendance

Contraintes :
- Doit être une app Laravel séparée (pas un simple virtual host)
- Port 8081
- BDD PostgreSQL dédiée
- Accès public (pas d'authentification)
- Les logs sont parsés depuis les fichiers rsyslog (/var/log/remote/)
- Doit réutiliser les conteneurs php et nginx existants

Questions :
1. Faut-il dupliquer le code laravel/ ou créer une nouvelle app ?
2. Comment gérer la BDD dédiée ?
3. Comment parser les fichiers rsyslog ?
4. Quelle architecture pour l'alimentation des logs ?
```
