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
| Branche évaluée | `feat_doc` |
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
| Statut | À renseigner |

Commande possible :

```bash
curl -I http://localhost:8080/login
```

### TV-02 — Création d'un compte utilisateur

| Élément | Description |
|---|---|
| Précondition | Page d'inscription accessible |
| Action | Créer un compte avec un nom, une adresse e-mail et un mot de passe valide |
| Résultat attendu | Le compte est créé et l'utilisateur est redirigé vers l'application |
| Log attendu | `auth.register` ou équivalent |
| Preuve à conserver | Capture + entrée en base ou dans le dashboard logs |
| Statut | À renseigner |

### TV-03 — Connexion réussie

| Élément | Description |
|---|---|
| Précondition | Compte utilisateur existant |
| Action | Se connecter avec les bons identifiants |
| Résultat attendu | L'utilisateur accède au questionnaire |
| Log attendu | `auth.login` niveau `info` |
| Preuve à conserver | Capture du dashboard + log correspondant |
| Statut | À renseigner |

### TV-04 — Connexion échouée

| Élément | Description |
|---|---|
| Précondition | Page de connexion accessible |
| Action | Entrer un mot de passe incorrect |
| Résultat attendu | Connexion refusée avec message d'erreur |
| Log attendu | `auth.failed` niveau `warning` |
| Preuve à conserver | Capture ou log d'avertissement |
| Statut | À renseigner |

### TV-05 — Affichage du questionnaire

| Élément | Description |
|---|---|
| Précondition | Utilisateur connecté |
| Action | Ouvrir `/dashboard` |
| Résultat attendu | Le questionnaire rsyslog s'affiche avec les questions prévues |
| Log attendu | Optionnel : accès route sensible si configuré |
| Preuve à conserver | Capture du questionnaire |
| Statut | À renseigner |

### TV-06 — Soumission du quiz

| Élément | Description |
|---|---|
| Précondition | Utilisateur connecté et questionnaire rempli |
| Action | Soumettre le questionnaire |
| Résultat attendu | Le score est calculé et les réponses sont enregistrées |
| Log attendu | `quiz.submit` avec score et utilisateur |
| Preuve à conserver | Capture du score + log de soumission |
| Statut | À renseigner |

### TV-07 — Déconnexion

| Élément | Description |
|---|---|
| Précondition | Utilisateur connecté |
| Action | Cliquer sur déconnexion |
| Résultat attendu | L'utilisateur revient sur la page de connexion |
| Log attendu | `auth.logout` niveau `info` |
| Preuve à conserver | Capture ou entrée de log |
| Statut | À renseigner |

### TV-08 — Réception d'un log par rsyslog

| Élément | Description |
|---|---|
| Précondition | Service rsyslog démarré |
| Action | Générer une action journalisée depuis l'application |
| Résultat attendu | Le log apparaît dans les fichiers rsyslog |
| Preuve à conserver | Sortie de commande |
| Statut | À renseigner |

Commandes possibles :

```bash
docker compose logs rsyslog --tail=50
docker compose exec rsyslog find /var/log/remote -type f -maxdepth 3
```

### TV-09 — Affichage des logs dans event-app

| Élément | Description |
|---|---|
| Précondition | Des logs existent |
| Action | Ouvrir `http://localhost:8081/event` |
| Résultat attendu | Les logs centralisés sont affichés avec pagination ou filtres |
| Preuve à conserver | Capture de la page |
| Statut | À renseigner |

### TV-10 — Filtrage des logs

| Élément | Description |
|---|---|
| Précondition | Plusieurs types de logs existent |
| Action | Filtrer par type, priorité ou date |
| Résultat attendu | La liste affiche uniquement les logs correspondant au filtre |
| Preuve à conserver | Capture avant/après filtrage |
| Statut | À renseigner |

### TV-11 — Purge des logs anciens

| Élément | Description |
|---|---|
| Précondition | Commande `logs:purge` disponible |
| Action | Exécuter la purge |
| Résultat attendu | Les logs antérieurs à 6 mois sont supprimés |
| Preuve à conserver | Sortie de commande ou test automatisé |
| Statut | À renseigner |

Commandes :

```bash
docker compose exec php php /var/www/laravel/artisan logs:purge
docker compose exec php php /var/www/event-app/artisan logs:purge
```

## Tableau de synthèse

| ID | Use case validé | Résultat attendu | Statut | Preuve |
|---|---|---|---|---|
| TV-01 | Accès login | Page HTTP 200 | À renseigner | Capture / curl |
| TV-02 | Inscription | Compte créé + log | À renseigner | Capture / BDD |
| TV-03 | Connexion | Accès dashboard + log | À renseigner | Capture / log |
| TV-04 | Échec connexion | Refus + warning | À renseigner | Log warning |
| TV-05 | Questionnaire | Questions affichées | À renseigner | Capture |
| TV-06 | Soumission quiz | Score + log | À renseigner | Capture / log |
| TV-07 | Déconnexion | Session fermée + log | À renseigner | Capture / log |
| TV-08 | rsyslog | Log reçu | À renseigner | Commande |
| TV-09 | Dashboard logs | Logs visibles | À renseigner | Capture |
| TV-10 | Filtres | Résultats filtrés | À renseigner | Capture |
| TV-11 | Purge | Logs anciens supprimés | À renseigner | Commande |

## Conclusion

Ces scénarios couvrent le parcours utilisateur complet et les fonctions principales liées à la journalisation. Les colonnes `Statut` et `Preuve` doivent être complétées avec les résultats réellement obtenus lors de l'exécution du projet.
