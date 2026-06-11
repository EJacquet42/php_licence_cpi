## Contexte — Analyse de l’existant

Le projet consiste à faire évoluer un site web de questionnaire sur le thème de **rsyslog**.
L’application permet à un utilisateur de créer un compte, de se connecter, d’accéder à un questionnaire, puis de soumettre ses réponses.
Le projet comprend également une partie liée à la consultation des événements générés par l’application.

Dans l’état initial du projet, les fonctionnalités principales du site sont présentes : authentification, création de compte, accès au questionnaire et rendu du quiz.
Cependant, les actions importantes réalisées par les utilisateurs ne sont pas suffisamment tracées.
Il est donc difficile de vérifier précisément quand un compte est créé, quand un utilisateur se connecte ou se déconnecte, ou encore quand un questionnaire est rendu.

Cette absence de journalisation limite la traçabilité de l’application.
En cas d’erreur, de tentative d’accès anormale ou de problème lors du rendu d’un questionnaire, il devient compliqué d’identifier l’origine du problème.
Le projet doit donc intégrer un système de logs permettant de centraliser et consulter les événements importants du site.

Le choix de **rsyslog** permet de répondre à ce besoin en mettant en place une solution de journalisation structurée, centralisée et exploitable.
Les logs produits par le site doivent permettre de suivre les actions critiques des utilisateurs, tout en facilitant la maintenance, le diagnostic et la sécurité de l’application.

## Expression du besoin

Le besoin principal est d’ajouter un système de journalisation au site web afin de conserver une trace fiable des actions importantes effectuées par les utilisateurs.

Les événements à journaliser sont les suivants :

* création d’un compte utilisateur
* connexion d’un utilisateur
* déconnexion d’un utilisateur
* accès au questionnaire
* soumission ou rendu d’un quiz
* erreurs éventuelles liées au rendu du questionnaire ou à l’authentification

Chaque log devra contenir les informations essentielles à l’analyse de l’événement :

* la date et l’heure de l’action
* le type d’événement
* l’identifiant ou l’adresse e-mail de l’utilisateur lorsque l’information est disponible
* l’adresse IP de l’utilisateur si possible
* un message clair décrivant l’action réalisée
* le niveau du log, par exemple `info`, `warning` ou `error`

Le système devra permettre de distinguer les événements normaux, comme une connexion réussie, des événements plus sensibles,
comme une tentative de connexion échouée ou une erreur lors de la soumission du questionnaire.

Les logs devront être exploitables par les développeurs et les administrateurs afin de faciliter le suivi de l’activité, la détection d’anomalies et le diagnostic des erreurs.

## Objectifs du projet

L’objectif général du projet est d’intégrer un système de logs fonctionnel dans le site web de questionnaire afin d’améliorer la traçabilité,
la sécurité et la maintenabilité de l’application.

Les objectifs détaillés sont les suivants :

| Objectif                            | Indicateur de réussite                                                              |
| ----------------------------------- | ----------------------------------------------------------------------------------- |
| Journaliser les créations de compte | Un log est généré à chaque inscription réussie                                      |
| Journaliser les connexions          | Un log est généré à chaque connexion réussie                                        |
| Journaliser les déconnexions        | Un log est généré à chaque déconnexion                                              |
| Journaliser le rendu des quiz       | Un log est généré à chaque soumission de questionnaire                              |
| Centraliser les logs avec rsyslog   | Les logs applicatifs sont transmis et consultables depuis le service prévu          |
| Faciliter le diagnostic             | Les logs contiennent une date, un type d’action, un utilisateur et un message clair |
| Valider le fonctionnement           | Des tests manuels permettent de prouver que chaque action génère bien un log        |

À la fin du projet, l’application devra permettre à un utilisateur de réaliser le parcours complet suivant : création de compte, connexion, réponse au questionnaire,
rendu du quiz, puis déconnexion. Chacune de ces étapes devra produire un log vérifiable.

## Contraintes techniques

Le projet repose sur une application web développée avec Laravel et exécutée dans un environnement Docker. L’infrastructure comprend une application principale de questionnaire,
une application ou interface dédiée à la consultation des logs, une base de données et un service rsyslog.

Les principales contraintes techniques sont les suivantes :

| Élément                   | Contrainte                                                                         |
| ------------------------- | ---------------------------------------------------------------------------------- |
| OS                        | Linux (Ubuntu utilisé pour le projet)                                              |
| Framework web             | Laravel / PHP                                                                      |
| Environnement             | Docker                                                                             |
| Journalisation            | rsyslog                                                                            |
| Application questionnaire | Accessible depuis le port prévu pour le site principal                             |
| Consultation des logs     | Interface ou application dédiée                                                    |
| Base de données           | Stockage des utilisateurs, questions, réponses et informations nécessaires au quiz |
| Sécurité                  | Les logs ne doivent pas contenir de mots de passe ou de données sensibles inutiles |
| Exploitation              | Les logs doivent être lisibles, datés et compréhensibles                           |