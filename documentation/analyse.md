# Analyse UML — Projet php_licence_cpi

## Objectif

Ce document présente les éléments de conception du projet : cas d'utilisation, déploiement Docker, schéma synoptique, sitemap complet et maquettes fonctionnelles. Il est construit pour correspondre aux routes et fonctionnalités réellement présentes dans les deux applications Laravel.

---

## Diagramme de cas d'utilisation

```plantuml
@startuml
left to right direction
actor "Utilisateur\n(Étudiant)" as User
actor "Évaluateur\n/ Administrateur" as Eval
actor "Système Docker\n/ rsyslog" as Sys

rectangle "Application questionnaire — port 8080" {
  usecase "Créer un compte" as UC_Register
  usecase "Se connecter" as UC_Login
  usecase "Réinitialiser le mot de passe" as UC_Reset
  usecase "Répondre au questionnaire" as UC_Quiz
  usecase "Soumettre le quiz" as UC_Submit
  usecase "Consulter ses logs" as UC_Event
  usecase "Générer un log manuel" as UC_Generator
  usecase "Gérer son profil" as UC_Profile
  usecase "Se déconnecter" as UC_Logout
}

rectangle "Dashboard logs — port 8081" {
  usecase "Consulter les logs centralisés" as UC_Logs
  usecase "Filtrer les logs" as UC_Filter
  usecase "Consulter les statistiques des questions" as UC_Stats
}

rectangle "Système de journalisation" {
  usecase "Recevoir les logs TCP/UDP 514" as UC_Rsyslog
  usecase "Archiver les logs" as UC_Archive
  usecase "Forward HTTP vers API" as UC_Forward
  usecase "Importer les fichiers de logs" as UC_Import
  usecase "Purger les logs anciens" as UC_Purge
}

User --> UC_Register
User --> UC_Login
User --> UC_Reset
User --> UC_Quiz
User --> UC_Submit
User --> UC_Event
User --> UC_Generator
User --> UC_Profile
User --> UC_Logout

Eval --> UC_Logs
Eval --> UC_Filter
Eval --> UC_Stats

Sys --> UC_Rsyslog
Sys --> UC_Archive
Sys --> UC_Forward
Sys --> UC_Import
Sys --> UC_Purge

UC_Register ..> UC_Rsyslog : génère log
UC_Login ..> UC_Rsyslog : génère log
UC_Logout ..> UC_Rsyslog : génère log
UC_Submit ..> UC_Rsyslog : génère log
UC_Generator ..> UC_Rsyslog : génère log
UC_Rsyslog ..> UC_Archive
UC_Rsyslog ..> UC_Forward
UC_Archive ..> UC_Import
UC_Import ..> UC_Logs
UC_Forward ..> UC_Logs
@enduml
```

---

## Diagramme de déploiement / blocs

```mermaid
flowchart LR
    subgraph Externe[Utilisateurs]
        E1[Navigateur étudiant]
        E2[Navigateur évaluateur]
    end

    subgraph DockerHost[Hôte Docker - réseau app-network 172.22.0.0/16]
        subgraph NGINX[nginx 172.22.0.50]
            N1[Port 8080 -> questionnaire]
            N2[Port 8081 -> dashboard logs]
        end

        subgraph PHP[php-fpm 172.22.0.40]
            LARAVEL[Application laravel /var/www/laravel]
            EVENTAPP[Application event-app /var/www/event-app]
        end

        RSYSLOG[rsyslog 172.22.0.10\nTCP/UDP 514]
        PG1[(postgres\nDB laravel\n172.22.0.20)]
        PG2[(postgres-event\nDB event\n172.22.0.60)]
        MYSQL[(mysql\n172.22.0.30)]

        VOL1[(Volume rsyslog-logs)]
        VOL2[(Volume postgres-data)]
        VOL3[(Volume event-postgres-data)]
        VOL4[(Volume mysql-data)]
    end

    E1 -->|HTTP 8080| N1
    E2 -->|HTTP 8081| N2
    NGINX -->|FastCGI 9000| PHP

    LARAVEL -->|SQL| PG1
    LARAVEL -->|SQL éventuel| MYSQL
    EVENTAPP -->|SQL| PG2

    LARAVEL -->|Logs TCP 514| RSYSLOG
    EVENTAPP -->|Lecture fichiers| VOL1
    RSYSLOG -->|Archive| VOL1
    RSYSLOG -->|Forward HTTP POST /api/logs| NGINX

    PG1 --> VOL2
    PG2 --> VOL3
    MYSQL --> VOL4
```

---

## Schéma synoptique du projet

```mermaid
flowchart TB
    A[Utilisateur] --> B[Application questionnaire :8080]
    B --> C[Authentification Laravel Breeze]
    B --> D[Questionnaire rsyslog]
    D --> E[Correction et calcul du score]
    E --> F[Génération d'un log quiz.submit]
    C --> G[Génération logs auth]
    F --> H[RsyslogService]
    G --> H
    H --> I[rsyslog TCP/UDP 514]
    I --> J[Archivage fichiers /var/log/remote]
    I --> K[Forward HTTP vers event-app]
    J --> L[Import fichiers logs]
    K --> M[API /api/logs]
    L --> N[Base event]
    M --> N
    N --> O[Dashboard logs :8081/event]
    N --> P[Statistiques :8081/questions-stats]
    O --> Q[Évaluateur]
    P --> Q
```

---

## Légende des flux

| Flux | Protocole | Direction |
|---|---|---|
| Navigation questionnaire | HTTP 8080 | Utilisateur → nginx |
| Navigation dashboard | HTTP 8081 | Évaluateur → nginx |
| Exécution PHP | FastCGI 9000 | nginx → php-fpm |
| Logs applicatifs | Syslog TCP 514 | Laravel → rsyslog |
| Logs conteneurs | Docker syslog driver | Conteneurs → rsyslog |
| Forward HTTP | HTTP POST `/api/logs` | rsyslog → event-app |
| Import fichiers | Lecture volume Docker | event-app → `/var/log/remote` |
| Requêtes SQL | PostgreSQL / MySQL | Applications → bases |

---

## Sitemap complet

```mermaid
flowchart TB
    subgraph Questionnaire[Application questionnaire - port 8080]
        ROOT1[/]
        LOGIN[/login]
        REGISTER[/register]
        FORGOT[/forgot-password]
        RESET[/reset-password/{token}]
        VERIFY[/verify-email]
        DASH[/dashboard]
        SUBMIT[POST /dashboard/submit]
        EVENT[/event]
        GENERATOR_GET[GET /generator]
        GENERATOR_POST[POST /generator]
        PROFILE_GET[GET /profile]
        PROFILE_PATCH[PATCH /profile]
        PROFILE_DELETE[DELETE /profile]
        LOGOUT[POST /logout]
        API_LOGS1[POST /api/logs]
    end

    subgraph EventApp[Application dashboard logs - port 8081]
        ROOT2[/]
        EVENT2[/event]
        STATS[/questions-stats]
        API_LOGS2[POST /api/logs]
    end

    ROOT1 --> LOGIN
    LOGIN --> DASH
    REGISTER --> DASH
    FORGOT --> RESET
    DASH --> SUBMIT
    DASH --> EVENT
    DASH --> GENERATOR_GET
    GENERATOR_GET --> GENERATOR_POST
    DASH --> PROFILE_GET
    PROFILE_GET --> PROFILE_PATCH
    PROFILE_GET --> PROFILE_DELETE
    DASH --> LOGOUT

    ROOT2 --> EVENT2
    EVENT2 --> STATS
```

---

## Maquettes fonctionnelles

### Maquette 1 — Connexion `/login`

```text
┌────────────────────────────────────────────┐
│ Connexion                                  │
├────────────────────────────────────────────┤
│ Email                                      │
│ [ utilisateur@example.com              ]   │
│ Mot de passe                               │
│ [ •••••••••••••••••                   ]   │
│                                            │
│ [ Se connecter ]                           │
│                                            │
│ Mot de passe oublié ?                      │
│ Pas encore de compte ? S'inscrire          │
└────────────────────────────────────────────┘
```

### Maquette 2 — Inscription `/register`

```text
┌────────────────────────────────────────────┐
│ Création de compte                         │
├────────────────────────────────────────────┤
│ Nom                                        │
│ [ Esteban                              ]   │
│ Email                                      │
│ [ esteban@example.com                  ]   │
│ Mot de passe                               │
│ [ •••••••••••••••••                   ]   │
│ Confirmation                              │
│ [ •••••••••••••••••                   ]   │
│                                            │
│ [ Créer le compte ]                        │
└────────────────────────────────────────────┘
```

### Maquette 3 — Questionnaire `/dashboard`

```text
┌────────────────────────────────────────────┐
│ Questionnaire rsyslog                      │
├────────────────────────────────────────────┤
│ Question 1 / 20                            │
│ Quel port utilise rsyslog par défaut ?     │
│ ( ) 80                                     │
│ (x) 514                                    │
│ ( ) 443                                    │
│                                            │
│ Question 2 / 20                            │
│ ...                                        │
│                                            │
│ [ Corriger ] [ Réinitialiser ] [ Envoyer ] │
└────────────────────────────────────────────┘
```

### Maquette 4 — Résultat du quiz

```text
┌────────────────────────────────────────────┐
│ Résultat du questionnaire                  │
├────────────────────────────────────────────┤
│ Score : 16 / 20                            │
│ Pourcentage : 80 %                         │
│                                            │
│ Réponses correctes : affichées en vert     │
│ Réponses incorrectes : affichées en rouge  │
│                                            │
│ Log généré : quiz.submit                   │
└────────────────────────────────────────────┘
```

### Maquette 5 — Générateur de logs `/generator`

```text
┌────────────────────────────────────────────┐
│ Générateur manuel de logs                  │
├────────────────────────────────────────────┤
│ Type d'événement                           │
│ [ auth.login ▼ ]                           │
│ Niveau                                     │
│ [ info ▼ ]                                 │
│ Message                                    │
│ [ Message de test                      ]   │
│                                            │
│ [ Générer le log ]                         │
└────────────────────────────────────────────┘
```

### Maquette 6 — Profil `/profile`

```text
┌────────────────────────────────────────────┐
│ Gestion du profil                          │
├────────────────────────────────────────────┤
│ Nom                                        │
│ [ Esteban                              ]   │
│ Email                                      │
│ [ esteban@example.com                  ]   │
│                                            │
│ [ Enregistrer ]                            │
│ [ Supprimer le compte ]                    │
└────────────────────────────────────────────┘
```

### Maquette 7 — Dashboard logs `/event`

```text
┌──────────────────────────────────────────────────────────────┐
│ Événements et logs                                           │
├──────────────────────────────────────────────────────────────┤
│ Type [Tous ▼] Priorité [Toutes ▼] Du [jj/mm] Au [jj/mm]      │
│ [ Filtrer ] [ Réinitialiser ]                                │
├──────────────────────────────────────────────────────────────┤
│ Date                Niveau    Type           Message         │
│ 10/06/2026 14:32    info      auth.login     Connexion OK    │
│ 10/06/2026 14:35    info      quiz.submit    Score 16/20     │
│ 10/06/2026 14:40    warning   auth.failed    Login refusé    │
├──────────────────────────────────────────────────────────────┤
│ ← Précédent   Page 1 / 8   Suivant →                         │
└──────────────────────────────────────────────────────────────┘
```

### Maquette 8 — Statistiques questions `/questions-stats`

```text
┌────────────────────────────────────────────┐
│ Statistiques des questions                 │
├────────────────────────────────────────────┤
│ Question | Réussite | Échecs | Taux        │
│ Q1       | 18       | 2      | 90 %        │
│ Q2       | 14       | 6      | 70 %        │
│ Q3       | 10       | 10     | 50 %        │
└────────────────────────────────────────────┘
```

---

## Cohérence routes / documentation

| Route | Application | Présente dans le sitemap | Fonction |
|---|---|---|---|
| `/login` | questionnaire | Oui | Connexion |
| `/register` | questionnaire | Oui | Inscription |
| `/dashboard` | questionnaire | Oui | Questionnaire |
| `POST /dashboard/submit` | questionnaire | Oui | Soumission quiz |
| `/event` | questionnaire | Oui | Logs personnels ou sensibles |
| `/generator` | questionnaire | Oui | Générateur de logs |
| `/profile` | questionnaire | Oui | Gestion profil |
| `/event` | event-app | Oui | Dashboard centralisé |
| `/questions-stats` | event-app | Oui | Statistiques questions |
| `POST /api/logs` | event-app | Oui | Réception logs |

## Conclusion

Cette analyse couvre les principales fonctionnalités et les routes des deux applications.
Les éléments de conception sont alignés avec l'infrastructure Docker, les usages attendus et les besoins de traçabilité du projet.