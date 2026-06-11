# Prompt — Configuration PHPStan et tests unitaires

## Date
10/06/2026

## Prompt

```
J'ai besoin de configurer PHPStan niveau 8 et d'écrire des tests unitaires
pour mon projet Laravel de questionnaire rsyslog.

Projet :
- laravel/ : app questionnaire (port 8080)
- event-app/ : app dashboard logs (port 8081)

Ce qu'il me faut :
1. Configuration PHPStan niveau 8 pour les deux apps
2. Tests Pest pour les services métier :
   - RsyslogService (envoi de logs à rsyslog)
   - DashboardSubmit (soumission questionnaire)
   - LogApi (réception API logs)
3. La config doit être stricte (level 8)

Peux-tu me donner les fichiers de configuration et les tests ?
```
