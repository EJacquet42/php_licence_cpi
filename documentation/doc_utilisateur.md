# Documentation utilisateur

## Objectif

Cette documentation décrit le parcours utilisateur de l'application de questionnaire rsyslog et du dashboard de consultation des logs.

Les captures doivent être ajoutées dans le dossier `doc/captures/`. Le précédent retour d'évaluation indiquait que les captures étaient absentes. Ce document référence donc les captures attendues. Elles doivent être produites à partir de l'application réelle, une fois le problème HTTP 500 corrigé.

## 1. Accéder au site

L'utilisateur accède à l'application principale depuis l'adresse :

```text
http://localhost:8080
```

Capture attendue :

```text
doc/captures/01_accueil_ou_login.png
```

## 2. Créer un compte

L'utilisateur ouvre la page d'inscription, saisit ses informations, puis valide le formulaire.

Résultat attendu :

- le compte est créé ;
- l'utilisateur peut accéder à l'application ;
- un log de création de compte est généré.

Capture attendue :

```text
doc/captures/02_creation_compte.png
```

## 3. Se connecter

L'utilisateur saisit son adresse e-mail et son mot de passe sur la page de connexion.

Résultat attendu :

- l'utilisateur est authentifié ;
- il est redirigé vers l'espace principal ;
- un log de connexion est généré.

Capture attendue :

```text
doc/captures/03_connexion.png
```

## 4. Répondre au questionnaire

Une fois connecté, l'utilisateur accède au questionnaire rsyslog et sélectionne ses réponses.

Résultat attendu :

- les questions sont affichées ;
- l'utilisateur peut sélectionner une réponse ;
- le formulaire peut être soumis.

Capture attendue :

```text
doc/captures/04_questionnaire.png
```

## 5. Rendre le quiz

L'utilisateur valide ses réponses en cliquant sur le bouton de soumission.

Résultat attendu :

- les réponses sont enregistrées ;
- le score ou le résultat est affiché ;
- un log de rendu du quiz est généré.

Capture attendue :

```text
doc/captures/05_resultat_quiz.png
```

## 6. Se déconnecter

L'utilisateur clique sur le bouton de déconnexion.

Résultat attendu :

- la session est fermée ;
- l'utilisateur revient à une page publique ou de connexion ;
- un log de déconnexion est généré.

Capture attendue :

```text
doc/captures/06_deconnexion.png
```

## 7. Consulter les logs

L'utilisateur autorisé accède au dashboard de consultation des logs depuis :

```text
http://localhost:8081
```

Résultat attendu :

- les logs sont visibles ;
- les événements peuvent être consultés ;
- l'accès au dashboard est protégé si l'utilisateur n'est pas authentifié.

Capture attendue :

```text
doc/captures/07_dashboard_logs.png
```

## 8. Vérifier la sécurité des logs

Les logs doivent être consultables sans exposer de données sensibles inutiles.

Les mots de passe ne doivent jamais apparaître dans les journaux.

Capture ou preuve attendue :

```text
doc/captures/08_logs_sans_mot_de_passe.png
```

## Liste de contrôle avant rendu

| Élément | Statut |
|---|---|
| Capture accueil/login ajoutée | À vérifier |
| Capture création compte ajoutée | À vérifier |
| Capture connexion ajoutée | À vérifier |
| Capture questionnaire ajoutée | À vérifier |
| Capture résultat quiz ajoutée | À vérifier |
| Capture déconnexion ajoutée | À vérifier |
| Capture dashboard logs ajoutée | À vérifier |
| Capture absence de mot de passe dans les logs ajoutée | À vérifier |

## Conclusion

Cette documentation doit être accompagnée de captures réelles. Sans ces captures, elle décrit le fonctionnement attendu mais ne constitue pas une preuve complète d'utilisation.