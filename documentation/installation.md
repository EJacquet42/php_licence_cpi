# Installation et configuration serveur

## Objectif

Ce document décrit l'installation du projet de questionnaire rsyslog et précise les vérifications à effectuer pour éviter l'erreur HTTP 500 liée à Vite.

## Prérequis

| Élément | Version ou contrainte |
|---|---|
| Docker | Installé et fonctionnel |
| Docker Compose | Installé et fonctionnel |
| Git | Installé |
| RAM | 4 Go minimum, 8 Go recommandés |
| Espace disque | 10 Go minimum |
| Ports | 8080, 8081 et 514 disponibles |
| Node.js / npm | Requis pour générer les assets Vite si le Dockerfile ne le fait pas |

## Installation

```bash
git clone https://github.com/EJacquet42/php_licence_cpi.git
cd php_licence_cpi
```

## Configuration des environnements

Vérifier les fichiers `.env` des deux applications.

Pour une version livrée ou évaluée :

```env
APP_DEBUG=false
```

Ne pas laisser `APP_DEBUG=true` dans le dépôt final.

## Build des assets Vite

Si l'application renvoie HTTP 500 avec une erreur liée à Vite ou à `manifest.json`, lancer :

```bash
cd laravel
npm ci
npm run build
cd ..

cd event-app
npm ci
npm run build
cd ..
```

Vérifier :

```bash
ls laravel/public/build/manifest.json
ls event-app/public/build/manifest.json
```

## Démarrage Docker

```bash
docker compose up -d --build
```

## Vérification des conteneurs

```bash
docker compose ps
```

Les conteneurs attendus sont notamment :

- application questionnaire ;
- application logs ;
- base de données ;
- rsyslog ;
- services associés au projet.

## Vérification HTTP

```bash
curl -I http://localhost:8080
curl -I http://localhost:8081
```

Résultat attendu :

- HTTP 200 pour une page accessible ;
- ou HTTP 302 si l'application redirige vers une page de connexion ;
- pas de HTTP 500.

## Preuves à conserver

```bash
mkdir -p documentation/preuves
curl -I http://localhost:8080 > documentation/preuves/smoke_8080.txt
curl -I http://localhost:8081 > documentation/preuves/smoke_8081.txt
docker compose ps > documentation/preuves/docker_ps.txt
```

## Problèmes connus

### HTTP 500 lié à Vite

Cause probable : assets non buildés, fichier `public/build/manifest.json` absent.

Correction : exécuter `npm ci && npm run build` dans chaque application utilisant Vite.

### Tests Pest qui échouent à cause de Vite

Correction : ajouter `$this->withoutVite();` dans les tests Feature ou dans `tests/TestCase.php`.

### Dashboard event-app accessible sans authentification

Correction : protéger les routes du dashboard avec le middleware `auth`.

## Conclusion

L'installation est considérée comme validée uniquement si Docker démarre correctement et si les deux applications répondent sans HTTP 500.