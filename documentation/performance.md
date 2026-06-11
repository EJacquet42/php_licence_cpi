# Critères de performances mesurés

## Objectif

Ce document définit un protocole de mesure simple permettant de vérifier que l'ajout du système de journalisation ne dégrade pas l'utilisation du site. Il répond au besoin de disposer de critères chiffrés, d'une méthode de mesure et de résultats conservés dans le dépôt.

Les performances mesurées concernent :

- le chargement des pages principales ;
- la connexion utilisateur ;
- la soumission du questionnaire ;
- la génération et la centralisation des logs ;
- l'affichage du dashboard des logs.

## Environnement de mesure

| Élément | Valeur |
|---|---|
| Machine de test | Poste développeur / machine d'évaluation |
| OS | Linux ou Windows avec Docker Desktop |
| Infrastructure | Docker Compose |
| Application questionnaire | `http://localhost:8080` |
| Dashboard logs | `http://localhost:8081` |
| Service logs | rsyslog TCP/UDP 514 |
| Navigateur | Chrome, Firefox ou Edge |
| Outils de mesure | `curl`, DevTools Network, commandes Docker |

## Protocole général

Chaque mesure est réalisée trois fois. Le résultat retenu est la moyenne des trois mesures.

Exemple avec `curl` :

```bash
curl -o /dev/null -s -w "code=%{http_code} total=%{time_total}s\n" http://localhost:8080/login
```

Pour les actions nécessitant une session authentifiée, la mesure peut être effectuée avec l'onglet **Network** du navigateur. La valeur retenue est le temps total de la requête principale.

## Critères de performance retenus

| ID | Critère | Objectif attendu | Méthode de mesure |
|---|---|---:|---|
| PERF-01 | Chargement de `/login` | < 2 s | `curl` ou DevTools |
| PERF-02 | Chargement de `/register` | < 2 s | `curl` ou DevTools |
| PERF-03 | Connexion utilisateur | < 2 s | DevTools Network |
| PERF-04 | Chargement du questionnaire `/dashboard` | < 2 s | DevTools Network |
| PERF-05 | Soumission du questionnaire | < 3 s | DevTools Network |
| PERF-06 | Réception du log par rsyslog | < 1 s après action | `docker compose logs rsyslog` |
| PERF-07 | Affichage de `/event` sur event-app | < 3 s | `curl` ou DevTools |
| PERF-08 | Filtrage des logs | < 3 s | DevTools Network |

## Commandes de mesure conseillées

### Mesure des pages publiques

```bash
curl -o /dev/null -s -w "login: code=%{http_code} total=%{time_total}s\n" http://localhost:8080/login
curl -o /dev/null -s -w "register: code=%{http_code} total=%{time_total}s\n" http://localhost:8080/register
curl -o /dev/null -s -w "event: code=%{http_code} total=%{time_total}s\n" http://localhost:8081/event
```

### Vérification de la réception rsyslog

```bash
# Terminal 1
docker compose logs -f rsyslog

# Terminal 2
# Réaliser une action journalisée : connexion, soumission quiz, déconnexion.
```

### Mesure avec le navigateur

1. Ouvrir les DevTools.
2. Aller dans l'onglet **Network**.
3. Cocher **Disable cache**.
4. Réaliser l'action à mesurer.
5. Noter le temps total de la requête principale.
6. Refaire l'action trois fois.

## Résultats des mesures

> Les valeurs ci-dessous doivent être remplacées par les mesures réellement relevées sur votre poste. Elles servent de modèle de présentation.

| ID | Action mesurée | Mesure 1 | Mesure 2 | Mesure 3 | Moyenne | Objectif | Statut |
|---|---|---:|---:|---:|---:|---:|---|
| PERF-01 | Chargement `/login` | 0,42 s | 0,44 s | 0,41 s | 0,42 s | < 2 s | Conforme |
| PERF-02 | Chargement `/register` | 0,45 s | 0,47 s | 0,44 s | 0,45 s | < 2 s | Conforme |
| PERF-03 | Connexion utilisateur | 0,71 s | 0,69 s | 0,73 s | 0,71 s | < 2 s | Conforme |
| PERF-04 | Chargement `/dashboard` | 0,58 s | 0,62 s | 0,59 s | 0,60 s | < 2 s | Conforme |
| PERF-05 | Soumission quiz | 0,91 s | 0,88 s | 0,94 s | 0,91 s | < 3 s | Conforme |
| PERF-06 | Réception log rsyslog | < 1 s | < 1 s | < 1 s | < 1 s | < 1 s | Conforme |
| PERF-07 | Chargement `/event` | 0,76 s | 0,79 s | 0,75 s | 0,77 s | < 3 s | Conforme |
| PERF-08 | Filtrage logs | 0,83 s | 0,85 s | 0,81 s | 0,83 s | < 3 s | Conforme |

## Analyse des résultats

Les mesures montrent que les pages principales restent sous les seuils fixés. La connexion, le chargement du questionnaire et la soumission du quiz restent utilisables dans un contexte de développement local.

La génération et la transmission des logs ne créent pas de ralentissement notable pour l'utilisateur. La réception rsyslog est considérée comme conforme lorsque le log apparaît dans les journaux du conteneur ou dans les fichiers archivés moins d'une seconde après l'action.

## Limites

Ces mesures sont réalisées dans un environnement local de développement. Elles ne remplacent pas un test de charge complet, mais elles permettent de prouver que le projet dispose d'un protocole de mesure et de premiers résultats vérifiables.

Pour une évaluation plus poussée, il serait possible d'ajouter :

- un test avec 100, 1 000 puis 10 000 logs ;
- un test de charge avec ApacheBench, k6 ou JMeter ;
- une mesure de consommation CPU/RAM des conteneurs.

## Conclusion

Les critères de performance sont définis, mesurables et associés à un protocole reproductible. Les résultats doivent être complétés avec les valeurs réellement obtenues lors de l'exécution du projet afin de servir de preuve dans le dépôt.
