## Contexte — Analyse de l’existant

Le projet consiste en un site web permettant à des utilisateurs de créer un compte, de se connecter, puis de répondre à un questionnaire en ligne.
Une fois authentifié, l’utilisateur accède au quiz et peut soumettre ses réponses afin qu’elles soient enregistrées et exploitées par l’application.

Dans l’état actuel, le site assure les fonctionnalités principales attendues : inscription, connexion, déconnexion, accès au questionnaire et rendu des réponses.
Cependant, certaines actions importantes réalisées par les utilisateurs ne sont pas suffisamment tracées.
Il est donc difficile de savoir précisément quand un utilisateur s’est connecté, déconnecté, a créé un compte ou a rendu un quiz.

Cette absence de journalisation limite la capacité à suivre l’activité du site, à détecter d’éventuels comportements anormaux,
à diagnostiquer des problèmes techniques ou à vérifier le bon déroulement des actions réalisées par les utilisateurs.
La mise en place de logs permettrait donc d’améliorer le suivi, la sécurité et la maintenance de l’application.

## Expression du besoin

Le besoin principal est d’ajouter un système de logs au site web afin de garder une trace des actions importantes effectuées par les utilisateurs.

Les événements à journaliser sont notamment :

* la connexion d’un utilisateur
* la déconnexion d’un utilisateur
* la création d’un compte
* le rendu ou la soumission d’un quiz

Chaque log devra contenir les informations nécessaires pour comprendre l’action réalisée, comme la date et l’heure de l’événement, le type d’action,
l’utilisateur concerné lorsque cela est possible, ainsi qu’un message décrivant l’événement.

Ce système de logs devra permettre aux administrateurs ou aux développeurs de consulter plus facilement l’historique des actions effectuées sur le site.
Il devra aussi faciliter l’identification d’erreurs, de problèmes de sécurité ou d’anomalies dans le comportement des utilisateurs.

## Objectif du projet

L’objectif du projet est d’intégrer un système de journalisation fiable et lisible au sein du site web.

Il y a 2 contraintes qui sont d'utiliser un OS Linux pour héberger l'application et Docker pour contenir les différentes parties.

Cette journalisation doit permettre de suivre les principales actions liées à l’utilisation du site, en particulier les actions d’authentification et de rendu de questionnaire.
Grâce à ces logs, il sera possible d’améliorer la traçabilité des événements, de renforcer la sécurité de l’application et de simplifier le diagnostic en cas de problème.

Le projet vise donc à rendre le site plus robuste, plus facilement maintenable et plus adapté à un suivi en environnement réel.
Les logs devront être suffisamment clairs pour être compris rapidement, tout en contenant les informations essentielles à l’analyse de l’activité du site.
