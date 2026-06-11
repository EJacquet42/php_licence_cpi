# Critères de performances mesurés

## Objectif

Ce document définit le protocole de mesure des performances de l'application de questionnaire rsyslog et du dashboard de logs.

Le précédent retour d'évaluation signale que des mesures indiquées comme `Conforme` étaient contredites par un retour HTTP 500. Ce document distingue donc clairement :

- l'état constaté avant correction ;
- les critères attendus ;
- le protocole reproductible ;
- les mesures à refaire après correction du problème Vite / HTTP 500.

## Environnement de mesure

| Élément | Valeur |
|---|---|
| Branche évaluée | `main` |
| Application principale | Laravel questionnaire |
| Application logs | event-app |
| Conteneurisation | Docker Compose |
| Ports applicatifs | 8080 pour le questionnaire, 8081 pour le dashboard logs |
| Service de journalisation | rsyslog |
| Outil de mesure | `curl` + navigateur, onglet Network |

## Critères attendus

| Action mesurée | Objectif attendu |
|---|---:|
| Chargement de la page de connexion | < 2 s |
| Connexion utilisateur | < 2 s |
| Chargement du questionnaire | < 2 s |
| Soumission du questionnaire | < 3 s |
| Génération d'un log après action | < 1 s |
| Consultation du dashboard logs | < 3 s |
| Consultation de l'API logs | < 2 s |

## Protocole de mesure reproductible

Les mesures doivent être faites uniquement lorsque les applications répondent sans erreur HTTP 500.

### 1. Démarrer l'environnement

```bash
docker compose up -d --build
```

### 2. Vérifier que les conteneurs sont démarrés

```bash
docker compose ps
```

### 3. Vérifier les codes HTTP

```bash
curl -I http://localhost:8080
curl -I http://localhost:8081
```

Les mesures de performance ne sont considérées comme valides que si les pages testées répondent avec un code HTTP 200 ou 302 selon le cas.

### 4. Mesurer le temps de réponse

```bash
curl -o /dev/null -s -w "code=%{http_code};time=%{time_total}s\n" http://localhost:8080
curl -o /dev/null -s -w "code=%{http_code};time=%{time_total}s\n" http://localhost:8081
```

### 5. Mesurer les pages nécessitant une session

Pour les pages nécessitant une authentification, les mesures sont réalisées depuis le navigateur avec l'onglet **Network**. La mesure retenue est le temps total de chargement de la requête principale.

## Résultat constaté avant correction

Le retour d'évaluation indique que les endpoints mesurés renvoyaient encore une erreur HTTP 500, probablement liée à Vite non buildé. Dans cet état, les mesures ne sont pas validables.

| Élément testé | Résultat constaté | Statut |
|---|---|---|
| Application questionnaire | HTTP 500 | Non conforme |
| Dashboard logs | HTTP 500 ou non validé | Non conforme |
| Mesures précédentes indiquées comme conformes | Contredites par le HTTP 500 | Non retenues |

## Mesures à renseigner après correction

Après correction du build Vite, remplir le tableau suivant avec les vraies valeurs obtenues.

| Date | Action mesurée | URL ou action | Code HTTP | Temps mesuré | Objectif | Statut | Preuve |
|---|---|---|---:|---:|---:|---|---|
| À renseigner | Page accueil / login | `http://localhost:8080` | À renseigner | À renseigner | < 2 s | À renseigner | Capture ou sortie curl |
| À renseigner | Dashboard logs | `http://localhost:8081` | À renseigner | À renseigner | < 3 s | À renseigner | Capture ou sortie curl |
| À renseigner | Connexion | Formulaire login | À renseigner | À renseigner | < 2 s | À renseigner | Capture Network |
| À renseigner | Chargement questionnaire | Page quiz | À renseigner | À renseigner | < 2 s | À renseigner | Capture Network |
| À renseigner | Soumission quiz | Formulaire quiz | À renseigner | À renseigner | < 3 s | À renseigner | Capture Network + log généré |
| À renseigner | Consultation API logs | Endpoint API logs | À renseigner | À renseigner | < 2 s | À renseigner | Sortie curl |

## Conclusion

Les performances ne doivent pas être déclarées conformes tant que les endpoints ne répondent pas correctement. La priorité est donc de corriger le problème Vite / HTTP 500, puis de rejouer le protocole ci-dessus et d'ajouter les preuves dans `documentation/preuves/`.
