# Conformité aux recommandations ANSSI — Journalisation rsyslog

## Objectif du document

Ce document présente la manière dont le projet de questionnaire rsyslog répond aux recommandations de journalisation inspirées des bonnes pratiques ANSSI. Il sert de livrable de preuve pour montrer quelles recommandations ont été prises en compte, où elles sont appliquées dans le projet et quelles limites restent identifiées.

Le projet met en place une application Laravel de questionnaire, une application de consultation des événements, un service rsyslog centralisé et une infrastructure Docker. Les logs applicatifs et systèmes doivent permettre de tracer les actions importantes : inscription, connexion, déconnexion, accès aux pages sensibles, soumission du quiz, réception et archivage des logs.

## Synthèse de conformité

| Référence | Recommandation suivie | Statut | Preuve attendue dans le dépôt |
|---|---|---:|---|
| R2 | Définir les événements à journaliser | Appliquée | `documentation/context_client.md`, `documentation/analyse.md` |
| R3 | Produire des journaux exploitables et structurés | Appliquée | Modèles `Log`, contrôleurs, exemples dans ce document |
| R4 | Horodater les événements | Appliquée | Champ `created_at` / dates dans les entrées de logs |
| R6 | Identifier l'origine de l'événement | Partielle | Utilisateur, type d'action, IP si disponible |
| R9 | Centraliser les journaux | Appliquée | `docker/rsyslog/rsyslog.conf`, `docker-compose.yml` |
| R15 | Protéger les journaux contre les accès non nécessaires | Partielle | Dashboard séparé, routes sensibles, absence de mot de passe dans les logs |
| R25 | Définir une durée de conservation | Appliquée | Commande `logs:purge`, rétention 6 mois |
| R26 | Prévoir l'exploitation et la recherche | Appliquée | Interface `event-app`, filtres par type/priorité/date |
| R27 | Superviser les erreurs et anomalies | Partielle | Logs `warning` / `error`, tests de validation à compléter |

## Détail des recommandations

### R2 — Définir les événements à journaliser

Le projet définit les événements fonctionnels et techniques devant être journalisés.

| Événement | Type conseillé | Gravité | Description |
|---|---|---|---|
| Création de compte | `auth.register` | `info` | Un utilisateur vient de créer un compte |
| Connexion réussie | `auth.login` | `info` | Un utilisateur authentifié accède à l'application |
| Échec de connexion | `auth.failed` | `warning` | Tentative de connexion non valide |
| Déconnexion | `auth.logout` | `info` | Fin de session utilisateur |
| Accès page sensible | `route.sensitive` | `info` | Consultation d'une page à tracer |
| Soumission quiz | `quiz.submit` | `info` ou `warning` | Questionnaire rendu avec score |
| Erreur d'envoi rsyslog | `log.forward_error` | `error` | Le log n'a pas pu être transmis à rsyslog |
| Purge des anciens logs | `log.purge` | `info` | Suppression des logs dépassant la durée de conservation |

### R3 — Produire des journaux exploitables et structurés

Chaque log doit être compréhensible sans avoir besoin de relire le code source. Le format minimal retenu est le suivant :

| Champ | Exemple | Rôle |
|---|---|---|
| `created_at` | `2026-06-10 14:32:10` | Date et heure de l'événement |
| `level` | `info`, `warning`, `error` | Niveau de gravité |
| `type` | `auth.login`, `quiz.submit` | Nature de l'action |
| `user_id` | `12` | Utilisateur concerné si connu |
| `ip_address` | `172.22.0.50` | Origine technique si disponible |
| `message` | `Connexion réussie pour user@example.com` | Description lisible |
| `context` | JSON | Données complémentaires non sensibles |

Les mots de passe, tokens de réinitialisation, clés d'API et secrets applicatifs ne doivent jamais apparaître dans les journaux.

### R4 — Horodater les événements

Les événements sont horodatés automatiquement par Laravel via les champs `created_at` et `updated_at`. Pour les logs exportés vers rsyslog, l'horodatage syslog permet également d'identifier le moment de réception de l'événement.

Preuve à fournir lors de l'évaluation :

```bash
docker compose exec php php /var/www/laravel/artisan tinker
>>> App\Models\Log::latest()->first(['type', 'message', 'created_at']);
```

### R6 — Identifier l'origine de l'événement

L'origine de l'événement doit être identifiable par au moins un des éléments suivants :

- identifiant utilisateur Laravel ;
- adresse e-mail si l'utilisateur est authentifié ;
- adresse IP ;
- nom du conteneur Docker pour les logs techniques ;
- service émetteur : `laravel`, `event-app`, `nginx`, `postgres`, `mysql`, `rsyslog`.

Limite actuelle : certains logs système ne sont pas directement liés à un utilisateur. Ce cas est normal et doit être indiqué avec un utilisateur `system` ou `null`.

### R9 — Centraliser les journaux

La centralisation est réalisée avec un conteneur rsyslog. Les applications et conteneurs Docker envoient leurs journaux vers le service rsyslog en TCP/UDP 514.

Preuves à citer :

- `docker-compose.yml` : service `rsyslog`, port `514/tcp` et `514/udp`, volume `rsyslog-logs` ;
- `docker/rsyslog/rsyslog.conf` : réception des logs et archivage ;
- `event-app` : import ou réception HTTP des événements centralisés.

Commande de vérification :

```bash
docker compose logs rsyslog --tail=50
docker compose exec rsyslog ls -R /var/log/remote
```

### R15 — Protéger les journaux

Les journaux peuvent contenir des informations utiles au diagnostic. Leur accès doit donc être limité et leur contenu maîtrisé.

Mesures prévues :

- ne pas stocker de mot de passe ;
- ne pas stocker de token sensible ;
- ne pas activer `APP_DEBUG=true` en production ;
- réserver les pages de consultation sensibles aux personnes autorisées ;
- séparer l'application questionnaire du dashboard de consultation.

Point de vigilance : si le dashboard est public pour l'évaluation, il faut l'indiquer clairement comme choix pédagogique et non comme configuration de production.

### R25 — Définir une durée de conservation

Le projet prévoit une conservation des logs pendant 6 mois. Les logs plus anciens sont supprimés par une commande Artisan.

Preuve à fournir :

```bash
docker compose exec php php /var/www/laravel/artisan logs:purge
docker compose exec php php /var/www/event-app/artisan logs:purge
```

Test conseillé : insérer un log daté de plus de 6 mois, exécuter la purge, puis vérifier qu'il a disparu.

### R26 — Exploiter et rechercher les journaux

L'application `event-app` doit permettre de consulter les logs centralisés. Les filtres à documenter sont :

- type d'événement ;
- niveau de gravité ;
- période ;
- utilisateur si disponible ;
- pagination.

Critère de réussite : un évaluateur doit pouvoir retrouver rapidement un événement de connexion ou de rendu de quiz depuis l'interface.

### R27 — Superviser les erreurs et anomalies

Les erreurs liées aux logs doivent elles-mêmes être tracées.

Exemples d'anomalies à journaliser :

| Situation | Type de log | Niveau |
|---|---|---|
| Échec d'envoi vers rsyslog | `log.forward_error` | `error` |
| Connexion refusée | `auth.failed` | `warning` |
| Erreur de validation quiz | `quiz.validation_error` | `warning` |
| Exception serveur | `app.exception` | `error` |

## Recommandations non entièrement couvertes

| Point à améliorer | Impact | Action proposée |
|---|---|---|
| `APP_DEBUG=true` en environnement évalué | Risque d'exposition d'informations techniques | Passer `APP_DEBUG=false` dans `.env.example` de production |
| Accès public au dashboard | Les logs peuvent être consultés trop largement | Ajouter une authentification si usage réel |
| Traçabilité des logs système | Certains logs ne contiennent pas d'utilisateur | Documenter `user = system` pour les événements techniques |
| Preuves de purge | Difficile de vérifier la rétention | Ajouter un test ou une capture de commande |

## Conclusion

Le projet couvre les besoins principaux de journalisation : définition des événements, génération de logs, centralisation avec rsyslog, archivage, consultation et purge. Les recommandations sont globalement appliquées pour un projet pédagogique. Les principales améliorations restantes concernent la sécurisation de la consultation des logs, la désactivation stricte du debug en production et l'ajout de preuves automatisées pour la purge et les erreurs.
