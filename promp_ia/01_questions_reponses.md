# Questions / Réponses — Projet php_licence_cpi

## Date
09/06/2026 — Session de cadrage avec l'utilisateur (2 rounds de questions).

---

## 1. Objectif général
Application web Laravel qui :
- Présente un **questionnaire** sur rsyslog (page `/dashboard`)
- **Génère des logs** à partir des réponses de l'utilisateur
- Envoie ces logs vers **rsyslog** (serveur distant dans le docker)
- Permet de **visualiser les logs** générés (page `/event`)
- Permet de **générer des logs manuellement** (page `/generator`)
- Système **multi-utilisateurs** avec authentification

## 2. Questionnaire → Génération de logs
- Le questionnaire actuel (questions rsyslog hardcodées dans `dashboard.blade.php`) **sert à générer des logs**
- Quand un utilisateur répond, les réponses sont transformées en logs et envoyées à rsyslog
- Double rôle : pédagogique + production de logs
- **1 log par soumission complète** du questionnaire (pas 1 par question)
- Contenu du log : question + réponse, correct/incorrect, score final, timestamp

## 3. Visualisation des logs (page /event)
- `/dashboard` = questionnaire + correction
- `/event` = page dédiée pour voir les logs générés
- Logs visibles par connexion utilisateur et par réponse au questionnaire
- **Auto refresh toutes les 10 secondes**
- Mode clair uniquement (pas de dark mode sur /event et /generator)

## 4. Générateur manuel de logs (page /generator)
- Page dédiée (route `/generator`)
- Formulaire structuré avec choix de **facility + priorité** rsyslog
- Permet d'écrire un message personnalisé
- POST vers `/generator`

## 5. Récupération des logs depuis rsyslog
- **Architecture Push** : rsyslog envoie les logs vers une API Laravel via `omhttp` (ou équivalent)
  - Si pas possible : lecture directe en base PostgreSQL
- Flux : Questionnaire/Generator → Stockage PostgreSQL (table `logs`) → Envoi à rsyslog → rsyslog push vers API Laravel → Affichage /event
- **Double sécurité** : stockage local (PostgreSQL) + envoi à rsyslog

## 6. Architecture technique
- **Backend** : Laravel (PHP 8.x)
- **Base de données Laravel** : PostgreSQL
- **Base MySQL** : présente dans le docker (possiblement pour rsyslog)
- **Frontend** : Blade + Tailwind CSS
- **Authentification** : Laravel Breeze (déjà en place)
- **Routes prévues** :
  - `GET /dashboard` → questionnaire
  - `POST /dashboard/submit` → soumission questionnaire
  - `GET /event` → visualisation logs
  - `GET /generator` → formulaire manuel
  - `POST /generator` → envoi log manuel

## 7. Infrastructure Docker
- Réseau : `app-network` (172.22.0.0/16) — changé de 172.20.0.0/16 pour éviter conflit avec locsio_default
- Tous les containers envoient déjà leurs logs au rsyslog via le driver syslog
- IPs statiques :
  - rsyslog : 172.22.0.10
  - postgres : 172.22.0.20
  - mysql : 172.22.0.30
  - php : 172.22.0.40
  - nginx : 172.22.0.50

## 8. Gestion des utilisateurs
- **Multi-utilisateurs**
- Authentification déjà en place (Breeze)
- Chaque utilisateur voit ses logs générés

## 9. Finalité
- **Pédagogique** (démonstration, TP) **ET outil fonctionnel**
- Doit pouvoir être présenté comme une démo complète de logging centralisé

## 10. Décisions prises
- [x] Questionnaire → génération de logs (1 log/soumission, contient question+réponse, correct/incorrect, score, timestamp)
- [x] Push : rsyslog → API Laravel (ou fallback lecture PostgreSQL)
- [x] Double stockage : PostgreSQL local + envoi rsyslog
- [x] Multi-utilisateurs
- [x] /dashboard = questionnaire, /event = visualisation, /generator = générateur manuel
- [x] Auto refresh /event : 10 secondes
- [x] Mode clair uniquement (sauf dashboard existant)
- [x] Générateur manuel avec facility + priorité + message personnalisé
- [x] Niveau de priorité rsyslog selon contexte (info pour bonne réponse, warning pour mauvaise)
- [x] Schéma table `logs` : id, user_id, type (quiz/manual), facility, priority, message, questions_data (JSON), score, total, created_at
- [x] Pagination : 50 logs par page sur /event
- [x] API logs : route publique (réseau docker interne uniquement)
- [x] Filtres avancés sur /event : type, utilisateur, date

## 11. Points à clarifier encore
- [ ] Configuration exacte de `omhttp` dans rsyslog.conf pour le push vers Laravel
- [ ] Design précis des pages /event et /generator

## 12. Prochaines étapes (suggestions)
1. [x] Créer la migration pour la table `logs` dans Laravel
2. [x] Créer le Model `Log`
3. [x] Créer les contrôleurs : `EventController`, `GeneratorController`, `DashboardController` (soumission)
4. [x] Créer les vues `/event` et `/generator`
5. [x] Configurer rsyslog pour le push HTTP (omhttp) vers Laravel
6. [x] Créer la route API dans Laravel pour recevoir les logs de rsyslog
7. [ ] Tester le flux complet (docker build + migration + navigation + soumission quiz + logs dans /event)
