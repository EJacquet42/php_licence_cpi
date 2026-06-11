# Documentation utilisateur — Questionnaire rsyslog et dashboard logs

## Objectif

Ce guide explique comment utiliser le site de questionnaire rsyslog et le dashboard de consultation des logs.
Il est destiné à un utilisateur étudiant, à un administrateur ou à un évaluateur.

## Accès aux applications

| Application | URL | Public concerné |
|---|---|---|
| Questionnaire rsyslog | `http://localhost:8080` | Étudiants / utilisateurs |
| Dashboard logs | `http://localhost:8081/event` | Évaluateur / administrateur |

## 1. Créer un compte

1. Ouvrir `http://localhost:8080/register`.
2. Renseigner le nom, l'adresse e-mail et le mot de passe.
3. Valider l'inscription.
4. Vérifier que l'utilisateur est redirigé vers l'application.

Résultat attendu : le compte est créé et un log d'inscription est généré.

Capture à ajouter : `doc/captures/register.png`.

## 2. Se connecter

1. Ouvrir `http://localhost:8080/login`.
2. Saisir l'adresse e-mail et le mot de passe.
3. Cliquer sur le bouton de connexion.

Résultat attendu : l'utilisateur arrive sur le questionnaire.

Capture à ajouter : `doc/captures/login.png`.

## 3. Répondre au questionnaire

1. Ouvrir la page `/dashboard` après connexion.
2. Lire chaque question du questionnaire rsyslog.
3. Sélectionner les réponses.
4. Cliquer sur le bouton de correction ou de soumission selon l'interface.

Résultat attendu : le score est calculé et la soumission est enregistrée.

Capture à ajouter : `doc/captures/questionnaire.png`.

## 4. Consulter son résultat

Après la soumission, l'application affiche le résultat du questionnaire.

Les informations attendues sont :

- score obtenu ;
- correction ou indication des réponses ;
- message de résultat ;
- log associé à la soumission.

Capture à ajouter : `doc/captures/resultat_quiz.png`.

## 5. Se déconnecter

1. Cliquer sur le bouton de déconnexion.
2. Vérifier que la session est fermée.
3. Vérifier que l'utilisateur revient sur la page de connexion.

Résultat attendu : un log de déconnexion est généré.

Capture à ajouter : `doc/captures/logout.png`.

## 6. Consulter les logs dans le dashboard

1. Ouvrir `http://localhost:8081/event`.
2. Observer la liste des événements.
3. Utiliser les filtres disponibles : type, priorité, date ou utilisateur selon l'interface.
4. Vérifier la présence des événements liés au parcours utilisateur.

Logs attendus après un parcours complet :

| Action | Log attendu |
|---|---|
| Inscription | `auth.register` |
| Connexion | `auth.login` |
| Accès questionnaire | `route.sensitive` ou équivalent |
| Soumission quiz | `quiz.submit` |
| Déconnexion | `auth.logout` |

Capture à ajouter : `doc/captures/dashboard_logs.png`.

## 7. Générer un log manuel si la page existe

Si la page `/generator` est disponible :

1. Se connecter.
2. Ouvrir `http://localhost:8080/generator`.
3. Générer un log de test.
4. Vérifier sa présence dans le dashboard de logs.

Capture à ajouter : `doc/captures/generator.png`.

## 8. Comprendre les niveaux de logs

| Niveau | Signification | Exemple |
|---|---|---|
| `info` | Action normale | Connexion réussie |
| `warning` | Action anormale mais non bloquante | Connexion échouée |
| `error` | Problème technique | Échec d'envoi vers rsyslog |

## 9. Bonnes pratiques utilisateur

- Ne jamais partager son mot de passe.
- Se déconnecter après utilisation sur un poste partagé.
- Signaler toute erreur ou absence de log lors d'une action importante.
- Vérifier que le quiz est bien soumis avant de quitter la page.

## Conclusion

Le parcours utilisateur principal est le suivant : inscription, connexion, réponse au questionnaire, soumission, consultation des logs, déconnexion.
Chaque étape importante doit être accompagnée d'un log afin de garantir la traçabilité du site.