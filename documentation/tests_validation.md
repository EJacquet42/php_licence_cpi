# Tests de validation des cas d'utilisation

## Objectif

Ce document sert à prouver que les cas d'utilisation principaux du projet ont été testés manuellement.

Le précédent retour indiquait que les scénarios existaient mais que la colonne `Statut` était encore `À renseigner`. Pour que le document soit une preuve, chaque test doit être exécuté et complété avec :

- un statut `Réussi` ou `Échec` ;
- une date d'exécution ;
- une preuve : capture, extrait de log, sortie `curl`, ou référence à un fichier de vérification.

## Environnement de validation

| Élément | Valeur |
|---|---|
| Branche testée | `main` |
| Application questionnaire | `http://localhost:8080` |
| Application logs | `http://localhost:8081` |
| Service logs | rsyslog |
| Navigateur | À renseigner |
| Date d'exécution | À renseigner |
| Exécuté par | À renseigner |

## Prérequis

Avant d'exécuter les tests :

1. les conteneurs Docker doivent être démarrés ;
2. l'application principale doit répondre sans HTTP 500 ;
3. l'application event-app doit répondre sans HTTP 500 ;
4. `APP_DEBUG=false` doit être défini dans les deux applications pour l'environnement livré ;
5. un utilisateur de test doit être disponible.

## Tableau de validation

| ID | Cas d'utilisation | Préconditions | Actions | Résultat attendu | Statut | Preuve |
|---|---|---|---|---|---|---|
| TV-01 | Créer un compte | Aucun compte existant avec l'e-mail choisi | Ouvrir la page d'inscription, saisir les informations, valider | Le compte est créé et un log de création de compte est généré | À exécuter | Capture inscription + extrait log |
| TV-02 | Se connecter | Compte utilisateur existant | Ouvrir la page de connexion, saisir e-mail et mot de passe, valider | L'utilisateur est connecté et un log de connexion est généré | À exécuter | Capture dashboard + extrait log |
| TV-03 | Refuser une connexion invalide | Compte existant | Saisir un mauvais mot de passe | La connexion est refusée et un log warning/error est généré | À exécuter | Capture erreur + extrait log |
| TV-04 | Accéder au questionnaire | Utilisateur connecté | Cliquer sur l'accès au quiz | Le questionnaire s'affiche | À exécuter | Capture quiz |
| TV-05 | Répondre au questionnaire | Utilisateur connecté, quiz affiché | Remplir les réponses | Les réponses sont sélectionnées et prêtes à être envoyées | À exécuter | Capture réponses |
| TV-06 | Rendre le quiz | Utilisateur connecté, réponses saisies | Cliquer sur le bouton de soumission | Les réponses sont enregistrées, le score est calculé et un log de rendu est généré | À exécuter | Capture résultat + extrait log |
| TV-07 | Se déconnecter | Utilisateur connecté | Cliquer sur déconnexion | La session est fermée et un log de déconnexion est généré | À exécuter | Capture retour login + extrait log |
| TV-08 | Consulter le dashboard logs | Utilisateur autorisé | Ouvrir l'application logs | La liste des logs est affichée | À exécuter | Capture dashboard logs |
| TV-09 | Protéger le dashboard logs | Utilisateur non connecté | Ouvrir directement `http://localhost:8081` | L'accès est refusé ou redirigé vers la connexion | À exécuter | Capture redirection/refus |
| TV-10 | Vérifier la purge des logs | Logs anciens disponibles | Lancer la commande de purge | Les logs plus anciens que la durée prévue sont supprimés | À exécuter | Sortie commande |
| TV-11 | Vérifier l'absence de données sensibles | Logs générés | Lire les derniers logs | Aucun mot de passe n'apparaît dans les logs | À exécuter | Extrait log anonymisé |

## Commandes utiles pour les preuves

```bash
# Vérifier les conteneurs
docker compose ps

# Vérifier les codes HTTP
curl -I http://localhost:8080
curl -I http://localhost:8081

# Mesurer les temps de réponse
curl -o /dev/null -s -w "code=%{http_code};time=%{time_total}s\n" http://localhost:8080
curl -o /dev/null -s -w "code=%{http_code};time=%{time_total}s\n" http://localhost:8081
```

## Conclusion

Ce document doit être complété après exécution réelle des tests. Les scénarios non exécutés ne doivent pas être indiqués comme réussis. Une preuve doit être associée à chaque test validé.