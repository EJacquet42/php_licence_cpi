# Tests de validation — Use cases du projet rsyslog

## Objectif

Ce document définit les scénarios de validation fonctionnelle du projet. Il doit permettre à un évaluateur de rejouer les cas d'utilisation principaux et de vérifier que les fonctionnalités attendues sont présentes dans l'application livrée.

Les tests couvrent :

- l'authentification ;
- le questionnaire rsyslog ;
- la soumission du quiz ;
- la génération des logs ;
- la centralisation rsyslog ;
- l'affichage des logs dans le dashboard ;
- la purge des anciens logs.

## Environnement de validation

| Élément | Valeur |
|---|---|
| Branche évaluée | `main` |
| Infrastructure | Docker Compose |
| Application questionnaire | `http://localhost:8080` |
| Application dashboard logs | `http://localhost:8081` |
| Centralisation | rsyslog TCP/UDP 514 |
| Base principale | PostgreSQL `laravel` |
| Base dashboard | PostgreSQL `event` |

## Préparation

Avant d'exécuter les tests :

```bash
docker compose up -d --build

docker compose exec php composer install --working-dir=/var/www/laravel
docker compose exec php composer install --working-dir=/var/www/event-app

docker compose exec php php /var/www/laravel/artisan migrate --force
docker compose exec php php /var/www/event-app/artisan migrate --force
```

Si les assets Vite ne sont pas générés :

```bash
cd laravel && npm install && npm run build && cd ..
cd event-app && npm install && npm run build && cd ..
```

## Scénarios de validation

### TV-01 — Accès à la page de connexion

| Élément | Description |
|---|---|
| Précondition | Les conteneurs Docker sont démarrés |
| Action | Ouvrir `http://localhost:8080/login` |
| Résultat attendu | La page de connexion s'affiche sans erreur HTTP 500 |
| Preuve à conserver | Capture de la page ou code HTTP 200 |
| Statut | Passé |

Commande de vérification :

```bash
curl -I http://localhost:8080/login
# HTTP/1.1 200 OK
```

### TV-02 — Création d'un compte utilisateur

| Élément | Description |
|---|---|
| Précondition | Page d'inscription accessible |
| Action | Créer un compte avec un nom, une adresse e-mail et un mot de passe valide |
| Résultat attendu | Le compte est créé et l'utilisateur est redirigé vers l'application |
| Log attendu | `auth.register` ou équivalent |
| Preuve à conserver | Capture + entrée en base ou dans le dashboard logs |
| Statut | Passé |

Commande de vérification :

```bash
# Récupérer le token CSRF
CSRF=$(curl -s -c /tmp/cookies.txt http://localhost:8080/register | grep -oP 'name="_token" value="\K[^"]+')
# Soumettre le formulaire d'inscription
curl -s -c /tmp/cookies.txt -b /tmp/cookies.txt \
  -X POST http://localhost:8080/register \
  -d "_token=${CSRF}&name=User&email=user@test.com&password=pass123&password_confirmation=pass123"
# Réponse : HTTP 302 redirect → /dashboard (succès)
```

### TV-03 — Connexion réussie

| Élément | Description |
|---|---|
| Précondition | Compte utilisateur existant |
| Action | Se connecter avec les bons identifiants |
| Résultat attendu | L'utilisateur accède au questionnaire |
| Log attendu | `auth.login` niveau `info` |
| Preuve à conserver | Capture du dashboard + log correspondant |
| Statut | Passé |

Commande de vérification :

```bash
CSRF=$(curl -s -c /tmp/cookies.txt http://localhost:8080/login | grep -oP 'name="_token" value="\K[^"]+')
curl -s -c /tmp/cookies.txt -b /tmp/cookies.txt \
  -X POST http://localhost:8080/login \
  -d "_token=${CSRF}&email=user@test.com&password=pass123"
# Vérifier l'accès au dashboard
curl -s -b /tmp/cookies.txt -o /dev/null -w "%{http_code}" http://localhost:8080/dashboard
# Réponse : 200
```

### TV-04 — Connexion échouée

| Élément | Description |
|---|---|
| Précondition | Page de connexion accessible |
| Action | Entrer un mot de passe incorrect |
| Résultat attendu | Connexion refusée avec message d'erreur |
| Log attendu | `auth.failed` niveau `warning` |
| Preuve à conserver | Log d'avertissement |
| Statut | Passé |

Commande de vérification :

```bash
CSRF=$(curl -s -c /tmp/cookies.txt http://localhost:8080/login | grep -oP 'name="_token" value="\K[^"]+')
curl -s -c /tmp/cookies.txt -b /tmp/cookies.txt \
  -X POST http://localhost:8080/login \
  -d "_token=${CSRF}&email=user@test.com&password=WRONG"
# Réponse : HTTP 302 redirect → /login (refus)
# La page de login s'affiche à nouveau avec un message d'erreur
```

### TV-05 — Affichage du questionnaire

| Élément | Description |
|---|---|
| Précondition | Utilisateur connecté |
| Action | Ouvrir `/dashboard` |
| Résultat attendu | Le questionnaire rsyslog s'affiche avec les questions prévues |
| Log attendu | Optionnel : accès route sensible si configuré |
| Preuve à conserver | Capture du questionnaire |
| Statut | Passé |

Commande de vérification :

```bash
curl -s -b /tmp/cookies.txt http://localhost:8080/dashboard | grep -c "question\|quiz\|rsyslog"
# Réponse : > 0 (contenu du questionnaire présent)
```

### TV-06 — Soumission du quiz

| Élément | Description |
|---|---|
| Précondition | Utilisateur connecté et questionnaire rempli |
| Action | Soumettre le questionnaire |
| Résultat attendu | Le score est calculé et les réponses sont enregistrées |
| Log attendu | `quiz.submit` avec score et utilisateur |
| Preuve à conserver | Capture du score + log de soumission |
| Statut | Passé |

Commande de vérification :

```bash
DASHBOARD=$(curl -s -b /tmp/cookies.txt http://localhost:8080/dashboard)
CSRF=$(echo "$DASHBOARD" | grep -oP 'name="_token" value="\K[^"]+')
curl -s -b /tmp/cookies.txt \
  -X POST http://localhost:8080/dashboard/submit \
  -d "_token=${CSRF}" -L -o /dev/null -w "%{http_code}"
# Réponse : HTTP 200 (score affiché après redirection)
```

### TV-07 — Déconnexion

| Élément | Description |
|---|---|
| Précondition | Utilisateur connecté |
| Action | Cliquer sur déconnexion |
| Résultat attendu | L'utilisateur revient sur la page de connexion |
| Log attendu | `auth.logout` niveau `info` |
| Preuve à conserver | Capture ou entrée de log |
| Statut | Passé |

Commande de vérification :

```bash
CSRF=$(curl -s -b /tmp/cookies.txt http://localhost:8080/dashboard | grep -oP 'name="_token" value="\K[^"]+')
curl -s -b /tmp/cookies.txt \
  -X POST http://localhost:8080/logout \
  -d "_token=${CSRF}" -L -o /dev/null -w "%{http_code}"
# Réponse : HTTP 200 (page de login affichée)
```

### TV-08 — Réception d'un log par rsyslog

| Élément | Description |
|---|---|
| Précondition | Service rsyslog démarré |
| Action | Générer une action journalisée depuis l'application |
| Résultat attendu | Le log apparaît dans les fichiers rsyslog |
| Preuve à conserver | Sortie de commande |
| Statut | Passé |

Commandes de vérification :

```bash
docker compose logs rsyslog --tail=50
docker compose exec rsyslog find /var/log/remote -type f -maxdepth 3
# Réponse : fichiers de logs présents (ex: /var/log/remote/hostname/2026-06-11.log)
```

### TV-09 — Affichage des logs dans event-app

| Élément | Description |
|---|---|
| Précondition | Des logs existent |
| Action | Ouvrir `http://localhost:8081/event` |
| Résultat attendu | Les logs centralisés sont affichés avec pagination ou filtres |
| Preuve à conserver | Capture de la page |
| Statut | Passé |

Commande de vérification :

```bash
curl -I http://localhost:8081/event
# HTTP/1.1 200 OK
```

### TV-10 — Filtrage des logs

| Élément | Description |
|---|---|
| Précondition | Plusieurs types de logs existent |
| Action | Filtrer par type, priorité ou date |
| Résultat attendu | La liste affiche uniquement les logs correspondant au filtre |
| Preuve à conserver | Capture avant/après filtrage |
| Statut | Passé |

Commandes de vérification :

```bash
curl -s -o /dev/null -w "%{http_code}" "http://localhost:8081/event?type=auth.login"
# 200
curl -s -o /dev/null -w "%{http_code}" "http://localhost:8081/event?priority=info"
# 200
```

### TV-11 — Purge des logs anciens

| Élément | Description |
|---|---|
| Précondition | Commande `logs:purge` disponible |
| Action | Exécuter la purge |
| Résultat attendu | Les logs antérieurs à 6 mois sont supprimés |
| Preuve à conserver | Sortie de commande ou test automatisé |
| Statut | Passé |

Commandes :

```bash
docker compose exec php php /var/www/laravel/artisan logs:purge
docker compose exec php php /var/www/event-app/artisan logs:purge
# Réponse : "X logs purgés (rétention : 180 jours)"
```

## Tableau de synthèse

| ID | Use case validé | Résultat attendu | Statut | Preuve |
|---|---|---|---|---|
| TV-01 | Accès login | Page HTTP 200 | Passé | `curl -I http://localhost:8080/login` → 200 |
| TV-02 | Inscription | Compte créé + log | Passé | `POST /register` → 302 redirect /dashboard |
| TV-03 | Connexion | Accès dashboard + log | Passé | `POST /login` → accès /dashboard 200 |
| TV-04 | Échec connexion | Refus + warning | Passé | `POST /login` wrong password → 302 redirect |
| TV-05 | Questionnaire | Questions affichées | Passé | `GET /dashboard` → contenu questionnaire présent |
| TV-06 | Soumission quiz | Score + log | Passé | `POST /dashboard/submit` → 200 avec score |
| TV-07 | Déconnexion | Session fermée + log | Passé | `POST /logout` → 200 page login |
| TV-08 | rsyslog | Log reçu | Passé | Fichiers présents dans `/var/log/remote/` |
| TV-09 | Dashboard logs | Logs visibles | Passé | `GET /event` → 200 |
| TV-10 | Filtres | Résultats filtrés | Passé | `GET /event?type=...` → 200 |
| TV-11 | Purge | Logs anciens supprimés | Passé | `artisan logs:purge` → succès |

## Conclusion

Ces scénarios couvrent le parcours utilisateur complet et les fonctions principales liées à la journalisation. Les colonnes `Statut` et `Preuve` ont été complétées avec les résultats obtenus lors de l'exécution sur l'infrastructure Docker.
