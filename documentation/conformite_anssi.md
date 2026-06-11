# Conformité aux recommandations ANSSI pour la journalisation

## Sources utilisées

Cette analyse s'appuie sur les documents suivants de l'ANSSI :

- **Recommandations de sécurité pour l'architecture d'un système de journalisation**, ANSSI, version publiée en janvier 2022 : https://messervices.cyber.gouv.fr/guides/recommandations-de-securite-pour-larchitecture-dun-systeme-de-journalisation
- **Guide d'hygiène informatique**, ANSSI, guide présentant 42 mesures d'hygiène numérique : https://messervices.cyber.gouv.fr/guides/guide-dhygiene-informatique

L'objectif n'est pas de prétendre que le projet respecte toutes les recommandations d'un système professionnel de supervision, mais de montrer quelles recommandations sont applicables au projet étudiant et comment elles sont prises en compte.

## Périmètre du projet

Le projet est un site web de questionnaire sur rsyslog comprenant :

- une application Laravel principale pour le questionnaire ;
- une application de consultation des logs ;
- une base de données ;
- un service rsyslog ;
- une infrastructure Docker.

Les événements suivis sont :

- création de compte ;
- connexion ;
- déconnexion ;
- accès au questionnaire ;
- rendu du quiz ;
- erreurs liées à l'authentification ou au rendu.

## Mapping des recommandations

| Réf. interne | Recommandation ANSSI retenue | Application dans le projet | Preuve attendue | Statut |
|---|---|---|---|---|
| R1 | Identifier les événements utiles à la sécurité | Les événements liés à l'authentification et au rendu du quiz sont définis | `documentation/context_client.md`, code des contrôleurs | Partiel |
| R2 | Centraliser les journaux | Les logs applicatifs sont transmis au service rsyslog | `docker/rsyslog/rsyslog.conf`, `docker-compose.yml` | Réalisé |
| R3 | Horodater les événements | Chaque log doit contenir une date et une heure | Format des logs applicatifs | Réalisé |
| R4 | Identifier la source de l'événement | Les logs contiennent l'utilisateur ou l'adresse e-mail si disponible | Exemples de logs | Réalisé |
| R5 | Distinguer les niveaux de gravité | Utilisation de niveaux `info`, `warning`, `error` | Code de génération des logs | Réalisé |
| R6 | Limiter les données sensibles dans les journaux | Les mots de passe ne doivent jamais être journalisés | Revue du code | Réalisé |
| R7 | Prévoir une durée de conservation | Purge automatique des anciens logs | `PurgeOldLogs.php` | Réalisé |
| R8 | Protéger l'accès aux journaux | Le dashboard de logs doit nécessiter une authentification | `event-app/routes/web.php` | À corriger |
| R9 | Désactiver le mode debug en environnement livré | `APP_DEBUG=false` dans les deux applications | `laravel/.env`, `event-app/.env` | À corriger |

## Points conformes

Le projet répond partiellement aux recommandations ANSSI sur la journalisation car il identifie les événements importants, centralise les logs avec rsyslog, horodate les événements et évite de journaliser les mots de passe.

Les événements choisis sont cohérents avec le besoin de traçabilité : création de compte, connexion, déconnexion et rendu du questionnaire.

## Points non conformes ou à corriger

### 1. Mode debug activé

Le retour d'évaluation indique que `APP_DEBUG=true` est encore présent dans `laravel/.env` et `event-app/.env`. Pour une version livrée, cette valeur doit être remplacée par :

```env
APP_DEBUG=false
```

Le mode debug peut afficher des informations internes en cas d'erreur. Il doit donc être désactivé dans l'environnement évalué.

### 2. Dashboard event-app sans authentification

Le dashboard des logs expose des événements applicatifs. Il doit être protégé par un middleware d'authentification.

Exemple attendu :

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
});
```

Si l'application event-app n'a pas de système d'authentification complet, il faut au minimum documenter cette limite dans ce fichier et expliquer la mesure prévue.

### 3. Doublon documentaire

Le retour d'évaluation signale un doublon strict entre `documentation/conformite_anssi.md` et `doc/anssi.md`. Il faut conserver un seul document de référence.

Choix recommandé :

- conserver `documentation/conformite_anssi.md` ;
- supprimer `doc/anssi.md` ;
- ou remplacer `doc/anssi.md` par un court fichier qui renvoie vers `documentation/conformite_anssi.md`.

## Conclusion

Le projet applique une partie des recommandations ANSSI liées à la journalisation : identification des événements, centralisation, horodatage, niveaux de gravité et rétention. Les deux corrections prioritaires sont la désactivation du mode debug et la protection du dashboard de consultation des logs.

Une fois ces corrections appliquées et prouvées dans le dépôt, le niveau de conformité sera plus solide car les affirmations documentaires ne seront plus contredites par les fichiers livrés.