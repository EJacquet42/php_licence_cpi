# Pré-requis développement logging — Projet php_licence_cpi

> Généré suite à l'analyse de `synthese_logs_a_garder.md` (ANSSI PA-012 v2.0)  
>et de l'existant du projet (Laravel 13, Docker, rsyslog).

---

## 1. Contexte du projet

| Question | Réponse |
|---|---|
| Projet | **php_licence_cpi** — Appli Laravel de quiz/logs |
| Type | App web (Breeze) + API REST interne (`POST /api/logs`) |
| Contexte | **Mise en conformité ANSSI** |
| Framework | **Laravel 13** (PHP 8.3) |
| Public visé | **Interne** (équipe / entreprise) |
| Priorité | **Critique** |

---

## 2. Infrastructure existante

### Stack technique (Docker, 5 conteneurs)

| Service | Rôle | IP |
|---|---|---|
| **rsyslog** | Collecteur syslog central + forward HTTP | `172.22.0.10:514` |
| **postgres** | Base principale (pgsql) | `172.22.0.20` |
| **mysql** | Base secondaire | `172.22.0.30` |
| **php** | Serveur Laravel (PHP-FPM) | `172.22.0.40` |
| **nginx** | Reverse proxy (port 8080) | `172.22.0.50` |

**Tous les conteneurs** (sauf rsyslog) utilisent le driver syslog Docker → `tcp://172.22.0.10:514`.

### Pipeline de logs existant

```
Laravel (RsyslogService)
    └─ TCP:514 ──→ rsyslog (172.22.0.10)
                        ├─ archive fichier /var/log/remote/
                        └─ HTTP POST ──→ nginx → Laravel POST /api/logs → DB postgres.logs

Conteneurs Docker (syslog driver)
    └─ TCP:514 ──→ rsyslog ──→ HTTP ──→ /api/logs ──→ DB
```

### Code existant

| Composant | Fichier | Rôle |
|---|---|---|
| **RsyslogService** | `app/Services/RsyslogService.php` | Envoie un message RFC 5424 à rsyslog via socket TCP |
| **LogApiController** | `app/Http/Controllers/LogApiController.php` | Reçoit les logs depuis rsyslog (omhttp) et les persist en DB |
| **Log (model)** | `app/Models/Log.php` | Modèle avec : `user_id`, `type`, `facility`, `priority`, `message`, `questions_data`, `score`, `total` |
| **Migration** | `database/migrations/2026_06_09_000001_create_logs_table.php` | Table `logs` en PostgreSQL |
| **DashboardController** | Quiz → crée un Log + appelle RsyslogService | Logs métier (résultats quiz) |
| **GeneratorController** | Formulaire manuel → crée un Log + appelle RsyslogService | Logs manuels |
| **EventController** | Liste/pagination/filtre des logs depuis la DB | Visualisation |

---

## 3. Constats ANSSI vs existant

### Ce qui est déjà conforme ✅

| Règle ANSSI | Statut | Détail |
|---|---|---|
| **R9 — Centralisation** | ✅ OK | rsyslog central, tous les conteneurs l'alimentent |
| **R26/R27 — Protection** | ✅ Partiel | Logs dans Docker, mais vérifier permissions rsyslog |
| **Authentification** | ✅ Partiel | Breeze gère login/logout, mais pas de logging des échecs/privilèges |
| **Horodatage (R3/R4)** | ✅ OK | RFC 5424 avec timestamp |

### Ce qui est à corriger / ajouter ❌

| Règle ANSSI | Problème | Correction |
|---|---|---|
| **R6 — Pas de debug en prod** | `LOG_LEVEL=debug` en production | Passer à `warning` ou `error` |
| **Logs applicatifs Laravel** | Channel `stack` → `single` (fichier local), pas dans rsyslog | Ajouter le channel `syslog` dans la stack ou rediriger vers RsyslogService |
| **Authentification : échecs/sessions** | Non loggé dans la table `logs` | Middleware ou listener sur les événements d'authentification Laravel |
| **Gestion des comptes** | Création/désactivation/verrouillage non loggés | Listener sur les événements `Registered`, `PasswordReset`, etc. |
| **Stratégies de sécurité** | Modification des stratégies d'audit non loggée | Ajouter un audit trail des changements de config |
| **Accès aux ressources sensibles** | Pas de logging d'accès aux ressources | Middleware optionnel sur les routes sensibles |
| **Données personnelles (Annexe D)** | Messages syslog bruts non filtrés | Vérifier qu'aucun email/IP sensible en clair |
| **Rétention (R25)** | Pas de mécanisme de purge à 6 mois | Ajouter un scheduler ou une tâche cron |

---

## 4. Périmètre fonctionnel des logs à implémenter

| Domaine ANSSI | Dans le code actuel | À ajouter |
|---|---|---|
| **Authentification** | Breeze gère les sessions, mais pas de logs métier | Logger les échecs de login, logout, utilisation de privilèges |
| **Gestion des comptes** | Inscription / reset password (Breeze) | Logger création, désactivation, changement de rôle, modif email/mdp |
| **Stratégies de sécurité** | Rien | Logger changement de config audit, suppression de logs |
| **Accès aux ressources sensibles** | Rien (⚠️ volumétrie) | Middleware optionnel, activable route par route |
| **Activité des processus** | RsyslogService envoie les messages (⚠️) | S'assurer que les démarrages/arrêts PHP/nginx sont captés (déjà via Docker syslog driver) |
| **Activité des systèmes** | Docker syslog driver capture déjà | Vérifier que le conteneur rsyslog loggue ses propres infos |

---

## 5. Rétention

| Critère | Valeur | Statut |
|---|---|---|
| Durée | **6 mois** (min. CNIL) | À implémenter |
| Mécanisme | Suppression auto via scheduler Laravel ou tâche cron | À implémenter |
| Logrotate | Configuration logrotate pour les fichiers rsyslog dans le volume Docker | À vérifier |

---

## 6. Configuration logging recommandée

### Production (`LOG_LEVEL`)
```
LOG_LEVEL=warning   # au lieu de debug
LOG_CHANNEL=stack
LOG_STACK=single,syslog   # Ajouter syslog pour rediriger vers rsyslog
```

### Alternative : tout passer par RsyslogService
Plutôt que d'utiliser le channel `syslog` de Laravel (qui écrit dans le syslog local du conteneur), on peut enrichir `RsyslogService` pour qu'il soit appelé par un channel Monolog custom, et centraliser **tous** les logs applicatifs via le pipeline existant.

---

## 7. À faire avant le développement

- [ ] **Auditer les données personnelles** dans les messages loggés (IP, email, payloads)
- [ ] **Passer `LOG_LEVEL=warning`** en production
- [ ] **Décider** : utiliser le channel `syslog` Laravel ou enrichir `RsyslogService` ?
- [ ] **Lister les événements d'authentification** à logger (échecs, verrouillage, privilèges)
- [ ] **Lister les ressources sensibles** de l'appli (routes admin, endpoints critiques)
- [ ] **Configurer la rétention** : scheduler Laravel (`Log::where('created_at', '<', now()->subMonths(6))->delete()`) + logrotate côté rsyslog
- [ ] **Vérifier les permissions** des logs rsyslog dans le volume Docker
- [ ] **Tester la volumétrie** : estimer le nombre de logs/jour et vérifier que rsyslog + PostgreSQL tiennent la charge

---

## 8. Décisions prises

| Date | Décision |
|---|---|
| 09/06/2026 | Mise en conformité ANSSI, priorité critique |
| 09/06/2026 | Périmètre : tous les domaines ANSSI |
| 09/06/2026 | Rétention : 6 mois |
| 09/06/2026 | Infrastructure existante conservée (rsyslog + Docker) |
