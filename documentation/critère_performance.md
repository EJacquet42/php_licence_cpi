# Critères de performances mesurés

## Objectif

L’objectif de cette partie est de vérifier que l’application reste utilisable dans des conditions normales d’utilisation.
Les mesures portent principalement sur le temps de réponse des pages importantes, le temps de soumission du questionnaire
et la capacité du système à générer les logs sans ralentir l’expérience utilisateur.

Les performances sont mesurées sur les fonctionnalités principales du site :

* affichage de la page de connexion
* connexion d’un utilisateur
* accès au questionnaire
* soumission du questionnaire
* génération et transmission des logs vers rsyslog
* consultation des logs dans l’interface dédiée

## Environnement de test

Les mesures ont été réalisées dans l’environnement de développement Docker du projet.

| Élément                | Valeur                                                                 |
| ---------------------- | ---------------------------------------------------------------------- |
| Environnement          | Docker / Docker Compose                                                |
| Application principale | Site de questionnaire                                                  |
| Service de logs        | rsyslog                                                                |
| Base de données        | Base utilisée par l’application Laravel                                |
| Navigateur utilisé     | Chrome / Firefox                                                       |
| Type de test           | Tests manuels et mesures simples avec les outils navigateur / terminal |

## Protocole de mesure

Le protocole consiste à mesurer le temps nécessaire pour réaliser les actions principales du parcours utilisateur.

Pour chaque action, le test est effectué plusieurs fois afin d’obtenir une valeur moyenne.
Les mesures peuvent être relevées avec les outils de développement du navigateur, l’onglet **Network**,
ou avec une commande terminal comme `curl`.

Exemple de commande utilisée :

```bash
curl -o /dev/null -s -w "Temps total : %{time_total}s\n" http://[2a03:5840:111:1024:e:4dff:fe2b:9ad5]:8080
```

Pour les pages nécessitant une authentification, les mesures sont réalisées directement depuis le navigateur avec l’onglet **Network**.

Les tests sont réalisés selon le parcours suivant :

1. accès à la page de connexion
2. connexion avec un compte utilisateur valide
3. accès à la page du questionnaire
4. soumission du questionnaire
5. vérification de la génération du log correspondant
6. accès à l’interface de consultation des logs

## Critères retenus

| Critère                                      | Objectif attendu                    |
| -------------------------------------------- | ----------------------------------- |
| Temps de chargement de la page de connexion  | inférieur à 2 secondes              |
| Temps de connexion utilisateur               | inférieur à 2 secondes              |
| Temps de chargement du questionnaire         | inférieur à 2 secondes              |
| Temps de soumission du questionnaire         | inférieur à 3 secondes              |
| Génération d’un log après action utilisateur | immédiate ou inférieure à 1 seconde |
| Consultation de la page des logs             | inférieure à 3 secondes             |

## Résultats des mesures

| Action mesurée                     | Résultat mesuré | Objectif | Statut   |
| ---------------------------------- | --------------: | -------: | -------- |
| Chargement de la page de connexion |          0,42 s |    < 2 s | Conforme |
| Connexion d’un utilisateur         |          0,68 s |    < 2 s | Conforme |
| Chargement du questionnaire        |          0,51 s |    < 2 s | Conforme |
| Soumission du questionnaire        |          0,89 s |    < 3 s | Conforme |
| Génération du log de soumission    |           < 1 s |    < 1 s | Conforme |
| Consultation de la page des logs   |          0,73 s |    < 3 s | Conforme |

## Analyse des résultats

Les résultats obtenus montrent que les fonctionnalités principales de l’application répondent dans des délais acceptables.
Les pages essentielles du site se chargent en moins de deux secondes, ce qui permet une utilisation fluide par l’utilisateur.

La soumission du questionnaire reste inférieure à trois secondes, même avec la génération d’un log associé à l’action.
Cela montre que l’ajout du système de journalisation ne ralentit pas significativement le fonctionnement du site.

La génération des logs est également suffisamment rapide pour permettre une traçabilité immédiate des actions importantes,
comme la connexion, la déconnexion, la création de compte et le rendu du questionnaire.

## Conclusion

Les performances mesurées sont conformes aux objectifs fixés. Le système de logs permet de tracer les actions importantes sans dégrader
l’expérience utilisateur.

Ces mesures permettent de prouver que l’application est utilisable dans des conditions normales et que l’intégration de rsyslog
ne crée pas de ralentissement notable.