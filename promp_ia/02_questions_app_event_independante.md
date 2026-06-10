# Questions / Réponses — App Event Indépendante

## Date
09/06/2026 — Dialogue avec l'utilisateur.

---

## Architecture

| Question | Réponse |
|----------|---------|
| **1. Architecture** | Nouvelle app Laravel séparée (pas un simple virtual host) |
| **2. Stack technique** | Même que l'autre : Laravel + Breeze + PostgreSQL |
| **3. Port** | **8081** |
| **4. Code source** | Dupliqué : copie du dossier `laravel/` → `event-app/` |
| **5. Base de données** | **Sa propre BDD PostgreSQL** — nouveau service dédié dans docker-compose |
| **6. Alimentation des logs** | **Option B** : parser les fichiers rsyslog (`/var/log/remote/`) depuis le volume Docker et importer dans sa propre BDD PostgreSQL |
| **7. Base dans rsyslog** | Nouveau service **postgres-event** dédié (pas dans rsyslog lui-même) |
| **8. Authentification** | **Publique** — pas de login requis |
| **9. Docker** | Réutiliser les conteneurs **php** et **nginx** existants + nouveau service **postgres-event** |

---

## Résumé décisions

| Point | Décision |
|-------|----------|
| App | Nouvelle app Laravel (code dupliqué) |
| Port | 8081 |
| Stack | Laravel + Breeze + PostgreSQL |
| BDD | Nouveau service postgres-event |
| Alimentation | Parse les fichiers rsyslog → import BDD |
| Auth | Publique (pas de login) |
| Conteneurs | php existant + nginx existant + nouveau postgres-event |
