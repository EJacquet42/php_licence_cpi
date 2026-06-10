# Analyse UML — Projet php_licence_cpi

## Diagramme de cas d'utilisation (Use Case)

```mermaid
---
title: Diagramme de cas d'utilisation — Dashboard PHP / Rsyslog
---
flowchart TB
    subgraph Systeme["Système php_licence_cpi"]
        direction TB

        subgraph AppPrincipale["Application questionnaire (port 8080)"]
            UC1["S'authentifier<br/>(login/register)"]
            UC2["Répondre au questionnaire rsyslog<br/>(/dashboard)"]
            UC3["Corriger le questionnaire"]
            UC4["Envoyer les logs du quiz<br/>vers rsyslog"]
        end

        subgraph EventApp["Dashboard évaluation (port 8081)"]
            UC5["Visualiser les logs centralisés<br/>de tous les utilisateurs"]
            UC6["Filtrer les logs<br/>(type, priorité, date)"]
        end

        subgraph Backend["Système back-end"]
            UC7["Collecter les logs<br/>(rsyslog :514 TCP/UDP)"]
            UC8["Forward HTTP vers API<br/>(omhttp → POST /api/logs)"]
            UC9["Archiver les logs<br/>dans /var/log/remote/"]
            UC10["Parser les fichiers rsyslog<br/>→ base postgres-event"]
            UC11["Purger les logs<br/>(rétention 6 mois)"]
        end
    end

    ActeurUtilisateur(["Utilisateur (Étudiant)"])
    ActeurEvaluateur(["Évaluateur (Professeur)"])
    ActeurSysteme(["Système (Docker/CRON)"])

    ActeurUtilisateur --- UC1
    ActeurUtilisateur --- UC2
    ActeurUtilisateur --- UC3
    ActeurUtilisateur --- UC4

    ActeurEvaluateur --- UC5
    ActeurEvaluateur --- UC6

    ActeurSysteme --- UC7
    ActeurSysteme --- UC8
    ActeurSysteme --- UC9
    ActeurSysteme --- UC10
    ActeurSysteme --- UC11

    UC4 -.->|"déclenche"| UC7
    UC7 -.->|"alimente"| UC8
    UC8 -.->|"alimente"| UC5
    UC7 -.->|"alimente"| UC9
    UC9 -.->|"alimente"| UC10
    UC10 -.->|"alimente"| UC5
```

---

## Diagramme de déploiement / blocs

```mermaid
---
title: Diagramme de déploiement — Infrastructure Docker
---
flowchart LR
    subgraph Externe["Utilisateurs"]
        Navigateur1["🧑 Navigateur<br/>Étudiant"]
        Navigateur2["🧑 Navigateur<br/>Évaluateur"]
    end

    subgraph DockerHost["Hôte Docker<br/>172.22.0.0/16"]
        subgraph Reseau["Réseau app-network"]
            direction TB

            Nginx["🧱 nginx<br/>172.22.0.50:80<br/>Ports hôte: 8080→80, 8081→8081"]

            subgraph PHP["PHP-FPM<br/>172.22.0.40"]
                AppQuestionnaire["App Questionnaire<br/>/var/www/laravel"]
                AppEvaluation["App Évaluation<br/>/var/www/event-app<br/>Dashboard logs"]
            end

            subgraph DB["Bases de données"]
                Postgres["🐘 postgres<br/>172.22.0.20<br/>DB: laravel"]
                Mysql["🐬 mysql<br/>172.22.0.30"]
                PostgresEvent["🐘 postgres-event<br/>172.22.0.60<br/>DB: event"]
            end

            Rsyslog["📝 rsyslog<br/>172.22.0.10<br/>Ports: 514 TCP/UDP"]
        end

        subgraph Stockage["Volumes Docker"]
            VolLogs["📁 rsyslog-logs<br/>/var/log/remote/"]
            VolPostgres["📁 postgres-data"]
            VolMysql["📁 mysql-data"]
            VolEvent["📁 event-postgres-data"]
        end
    end

    Navigateur1 -->|"HTTP 8080"| Nginx
    Navigateur2 -->|"HTTP 8081"| Nginx

    Nginx -->|"fastcgi :9000"| PHP

    AppQuestionnaire -->|"TCP :514"| Rsyslog
    AppQuestionnaire -->|"SQL"| Postgres
    AppQuestionnaire -->|"SQL"| Mysql

    AppEvaluation -->|"lecture fichiers"| VolLogs
    AppEvaluation -->|"SQL"| PostgresEvent

    Rsyslog -->|"écriture"| VolLogs
    Rsyslog -->|"HTTP POST /api/logs"| Nginx
    Nginx -->|"fastcgi :9000"| AppQuestionnaire

    Postgres -->|"logs Docker<br/>TCP :514"| Rsyslog
    Mysql -->|"logs Docker<br/>TCP :514"| Rsyslog
    PHP -->|"logs Docker<br/>TCP :514"| Rsyslog
    Nginx -->|"logs Docker<br/>TCP :514"| Rsyslog
```

---

## Schéma synoptique

```mermaid
---
title: Schéma synoptique — Flux fonctionnel des logs
---
flowchart TB
    subgraph Utilisateurs["Utilisateurs"]
        Etudiant["🧑 Étudiant<br/>Navigateur web"]
        Professeur["🧑 Professeur<br/>Navigateur web"]
    end

    subgraph Frontend["Couche présentation — nginx"]
        Quiz["📋 Questionnaire rsyslog<br/>port 8080 /dashboard"]
        EvalDashboard["📈 Dashboard évaluation<br/>port 8081 /event"]
    end

    subgraph Logique["Couche métier — PHP/Laravel"]
        Correction["✓ Correction du quiz<br/>Calcul du score"]
        RsyslogService["📤 RsyslogService<br/>Envoi TCP :514"]
        LogImport["📥 Import fichiers rsyslog<br/>Parser → BDD"]
        LogApi["🌐 API REST<br/>POST /api/logs"]
    end

    subgraph Collecte["Couche collecte — rsyslog"]
        SyslogCollect["📡 Collecte syslog<br/>TCP/UDP :514"]
        ArchiveLogs["💾 Archivage fichiers<br/>/var/log/remote/"]
        ForwardHTTP["↗️ Forward HTTP<br/>omhttp → POST /api/logs"]
    end

    subgraph Stockage["Couche stockage"]
        DB_Quiz["🐘 PostgreSQL<br/>Table logs<br/>(app questionnaire)"]
        DB_Event["🐘 PostgreSQL<br/>Table logs<br/>(app évaluation)"]
        FilesLogs["📁 Fichiers .log"]
    end

    subgraph Retention["Couche rétention"]
        Purge["🧹 Purge automatique<br/>Scheduler Laravel<br/>6 mois"]
    end

    %% Flux utilisateur
    Etudiant -->|"HTTP 8080"| Quiz
    Professeur -->|"HTTP 8081"| EvalDashboard

    %% Logique métier
    Quiz -->|"Soumission"| Correction
    Correction -->|"Log type=quiz"| RsyslogService

    %% Flux syslog
    RsyslogService -->|"TCP :514"| SyslogCollect
    SyslogCollect -->|"Écriture"| ArchiveLogs
    ArchiveLogs -->|"Stockage"| FilesLogs
    SyslogCollect -->|"Forward"| ForwardHTTP

    %% Flux API
    ForwardHTTP -->|"HTTP POST"| LogApi
    LogApi -->|"Insertion"| DB_Quiz

    %% Flux event-app
    FilesLogs -->|"Lecture périodique"| LogImport
    LogImport -->|"Insertion"| DB_Event
    DB_Event -->|"Lecture"| EvalDashboard

    %% Rétention
    DB_Quiz --> Purge
    DB_Event --> Purge
    FilesLogs -.->|"logrotate"| Purge

    %% Style
    classDef user fill:#e1f5fe,stroke:#0288d1,stroke-width:2px
    classDef front fill:#fff3e0,stroke:#f57c00,stroke-width:2px
    classDef logic fill:#e8f5e9,stroke:#388e3c,stroke-width:2px
    classDef collect fill:#fce4ec,stroke:#d32f2f,stroke-width:2px
    classDef storage fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px
    classDef retention fill:#fff8e1,stroke:#f9a825,stroke-width:2px

    class Etudiant,Professeur user
    class Quiz,EvalDashboard front
    class Correction,RsyslogService,LogImport,LogApi logic
    class SyslogCollect,ArchiveLogs,ForwardHTTP collect
    class DB_Quiz,DB_Event,FilesLogs storage
    class Purge retention
```

---

## Légende des flux

| Flux | Protocole | Direction |
|------|-----------|-----------|
| Navigation questionnaire | HTTP 8080 | Étudiant → nginx |
| Navigation évaluation | HTTP 8081 | Évaluateur → nginx |
| Exécution PHP | FastCGI :9000 | nginx → php-fpm |
| Logs applicatifs | Syslog TCP :514 | PHP → rsyslog |
| Logs conteneurs | Docker syslog driver → TCP :514 | Tous conteneurs → rsyslog |
| Forward HTTP | HTTP POST /api/logs | rsyslog → nginx → Laravel |
| Import fichiers | Lecture volume Docker | event-app → /var/log/remote/ |
| Requêtes SQL | PostgreSQL / MySQL | Laravel/event-app → BDD |

---

## Sitemap (Plan du site)

```mermaid
---
title: Sitemap — Architecture des pages
---
flowchart TB
    subgraph AppQuestionnaire["Application questionnaire — port 8080"]
        direction TB

        Accueil["/ <br/>↳ redirection vers /login"]

        subgraph Auth["Pages publiques (non connecté)"]
            Login["/login<br/>Connexion"]
            Register["/register<br/>Inscription"]
            Forgot["/forgot-password<br/>Mot de passe oublié"]
            Reset["/reset-password/{token}<br/>Réinitialisation"]
        end

        subgraph App["Pages authentifiées"]
            Dashboard["/dashboard<br/>Questionnaire rsyslog<br/>↳ Correction + envoi logs"]
        end

        subgraph API["API interne (réseau Docker)"]
            ApiLogs["POST /api/logs<br/>Réception logs depuis rsyslog"]
            Submit["POST /dashboard/submit<br/>Soumission questionnaire"]
        end

        Accueil --> Login
        Accueil --> Register
        Login --> Dashboard
        Register --> Dashboard
    end

    subgraph AppEvaluation["Application évaluation — port 8081"]
        direction TB

        Home["/ <br/>↳ redirection vers /event"]
        EvalEvent["/event<br/>Dashboard logs centralisés<br/>↳ Filtres : type, priorité, date<br/>↳ Pagination 50/page<br/>↳ Public (sans auth)"]
        EvalApi["POST /api/logs<br/>Réception logs depuis rsyslog"]
    end

    AppQuestionnaire -.->|"même conteneur php"| AppEvaluation
```

---

## Mockups (Maquettes fonctionnelles)

### Mockup 1 — Page de connexion (/login)

```mermaid
---
title: Mockup — Connexion
---
flowchart TB
    subgraph Page["Page de connexion"]
        direction TB

        Card["
        ┌──────────────────────────────────┐
        │                                  │
        │    🔐 Connexion                  │
        │                                  │
        │    Email                         │
        │    ┌──────────────────────────┐  │
        │    │  email@example.com       │  │
        │    └──────────────────────────┘  │
        │                                  │
        │    Mot de passe                  │
        │    ┌──────────────────────────┐  │
        │    │  •••••••••••             │  │
        │    └──────────────────────────┘  │
        │                                  │
        │    ┌──────────────────────────┐  │
        │    │  Se connecter            │  │
        │    └──────────────────────────┘  │
        │                                  │
        │    Mot de passe oublié ?         │
        │    Vous n'avez pas de compte ?   │
        │    → S'inscrire                  │
        └──────────────────────────────────┘
        "]
    end
```

### Mockup 2 — Questionnaire (/dashboard)

```mermaid
---
title: Mockup — Questionnaire rsyslog
---
flowchart TB
    subgraph Page["Page Questionnaire"]
        direction TB

        Header["
        ┌─────────────────────────────────────────────┐
        │  Questionnaire : fonctionnement des logs    │
        │  avec rsyslog                               │
        └─────────────────────────────────────────────┘
        "]

        Intro["
        ┌─────────────────────────────────────────────┐
        │  Réponds aux questions ci-dessous, puis     │
        │  clique sur le bouton de correction.        │
        └─────────────────────────────────────────────┘
        "]

        Q1["
        ┌─────────────────────────────────────────────┐
        │  1. Quel port utilise rsyslog par défaut ?  │
        │                                             │
        │  ○ 80                                       │
        │  ● 514                                      │
        │  ○ 443                                      │
        │  ○ 8080                                     │
        │                                             │
        │  ┌─────────────────────────────────────┐    │
        │  │  ✅ Bonne réponse.  Le port par     │    │
        │  │  défaut de rsyslog est le 514.      │    │
        │  └─────────────────────────────────────┘    │
        └─────────────────────────────────────────────┘
        "]

        Q2["
        ┌─────────────────────────────────────────────┐
        │  2. Quel est le rôle d'omhttp ?             │
        │                                             │
        │  ○ Gérer les connexions HTTP entrantes      │
        │  ● Forwarder des logs via HTTP              │
        │  ○ Chiffrer les messages syslog             │
        │  ○ Filtrer les logs par priorité            │
        │                                             │
        │  ┌─────────────────────────────────────┐    │
        │  │  ❌ Mauvaise réponse. omhttp per-   │    │
        │  │  met à rsyslog d'envoyer des logs   │    │
        │  │  vers une API HTTP.                 │    │
        │  └─────────────────────────────────────┘    │
        └─────────────────────────────────────────────┘
        "]

        Buttons["
        ┌─────────────────────────────────────────────┐
        │  [Corriger]  [Réinitialiser]  [Envoyer ✓]  │
        └─────────────────────────────────────────────┘
        "]

        Score["
        ┌─────────────────────────────────────────────┐
        │  Score : 1 / 2 — 50%                        │
        │  Résultat correct, mais quelques notions    │
        │  sont à revoir.                             │
        └─────────────────────────────────────────────┘
        "]

        Header --> Intro --> Q1 --> Q2 --> Buttons --> Score
    end
```

### Mockup 3 — Dashboard évaluation (port 8081 /event)

```mermaid
---
title: Mockup — Dashboard évaluation (port 8081)
---
flowchart TB
    subgraph Page["Page Dashboard Évaluation"]
        direction TB

        Title["
        ┌─────────────────────────────────────────────┐
        │  Événements & Logs — Évaluation            │
        └─────────────────────────────────────────────┘
        "]

        Filters["
        ┌─────────────────────────────────────────────┐
        │  Type    Priorité    Du          Au         │
        │  ┌────┐  ┌───────┐  ┌────────┐ ┌────────┐  │
        │  │Tous│  │Toutes │  │jj/mm  │ │jj/mm  │  │
        │  └────┘  └───────┘  └────────┘ └────────┘  │
        │  [Filtrer]  [Réinitialiser]                │
        └─────────────────────────────────────────────┘
        "]

        Log1["
        ┌─────────────────────────────────────────────┐
        │  [warning]  [auth]  [quiz]  #42  système    │
        │  Score 2/5 - Quel port utilise rsyslog...   │
        │  ▸ Voir le détail des réponses (2/5)        │
        │                              10/06/2026     │
        └─────────────────────────────────────────────┘
        "]

        Log2["
        ┌─────────────────────────────────────────────┐
        │  [info]  [local0]  [system]  système         │
        │  Log système depuis le conteneur nginx      │
        │                              10/06/2026     │
        └─────────────────────────────────────────────┘
        "]

        Log3["
        ┌─────────────────────────────────────────────┐
        │  [error]  [kern]  [system]  système         │
        │  Kernel error from container php            │
        │                              10/06/2026     │
        └─────────────────────────────────────────────┘
        "]

        Pagination["
        ┌─────────────────────────────────────────────┐
        │  ← Précédente   1 2 3 ... 8   Suivante →   │
        └─────────────────────────────────────────────┘
        "]

        Note["
        ℹ️ Page publique — aucune authentification requise
        "]

        Title --> Filters --> Log1 --> Log2 --> Log3 --> Pagination
        Filters --> Note
    end
```
