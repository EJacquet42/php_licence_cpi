# Prompt — Purge et rétention des logs

## Date
10/06/2026

## Prompt

```
J'ai besoin d'implémenter une commande de purge des logs
pour mon projet Laravel de logging rsyslog.

Contraintes :
- Conservation : 6 mois (recommandation ANSSI)
- Doit fonctionner sur laravel/ et event-app/
- Commande Artisan : logs:purge
- Les logs plus vieux que la durée définie doivent être supprimés
- Doit journaliser l'action de purge

Peux-tu me donner le code de la commande et du test associé ?
```
