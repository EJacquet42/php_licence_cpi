## Planning de production du logiciel et des logs

Période : du **lundi 8 juin à 12h** au **vendredi 12 juin à 12h**.

| Date / horaire               | Tâches prévues                                                                            | Livrable attendu                    | Responsable principal |
| ---------------------------- | ----------------------------------------------------------------------------------------- | ----------------------------------- | --------------------- |
| Lundi 8 juin — 12h à 14h     | Analyse du besoin, identification des événements à journaliser, répartition des tâches    | Liste des événements à logger       | Équipe                |
| Lundi 8 juin — 14h à 17h     | Analyse de l’existant : routes Laravel, contrôleurs, authentification, rendu du quiz      | Document d’analyse de l’existant    | Esteban               |
| Mardi 9 juin — 8h à 10h      | Mise en place ou vérification de l’environnement Docker et rsyslog                        | Environnement fonctionnel           | Léo                   |
| Mardi 9 juin — 10h à 12h     | Ajout des logs lors de la création de compte et de la connexion                           | Logs inscription / connexion        | Edouard               |
| Mardi 9 juin — 13h à 15h     | Ajout des logs lors de la déconnexion                                                     | Log de déconnexion                  | Edouard               |
| Mardi 9 juin — 15h à 17h     | Ajout des logs lors de l’accès au questionnaire et du rendu du quiz                       | Logs questionnaire / rendu quiz     | Esteban               |
| Mercredi 10 juin — 8h à 10h  | Configuration de l’envoi des logs vers rsyslog                                            | Logs transmis à rsyslog             | Léo                   |
| Mercredi 10 juin — 10h à 12h | Vérification du format des logs : date, utilisateur, action, message, niveau              | Format de log validé                | Équipe                |
| Mercredi 10 juin — 13h à 15h | Création ou amélioration de l’interface de consultation des logs                          | Page ou dashboard de consultation   | Léo / Esteban         |
| Mercredi 10 juin — 15h à 17h | Tests manuels du parcours complet : inscription, connexion, quiz, déconnexion             | Résultats des tests manuels         | Équipe                |
| Jeudi 11 juin — 8h à 10h     | Correction des bugs détectés lors des tests                                               | Application corrigée                | Équipe                |
| Jeudi 11 juin — 10h à 12h    | Rédaction de la documentation d’installation et d’utilisation                             | README / documentation utilisateur  | Edouard               |
| Jeudi 11 juin — 13h à 15h    | Rédaction des preuves de validation : captures, commandes, exemples de logs               | Document de preuves                 | Esteban               |
| Jeudi 11 juin — 15h à 17h    | Finalisation de la gestion des risques, des indicateurs et du planning                    | Documents de gestion de projet      | Équipe                |
| Vendredi 12 juin — 8h à 10h  | Relecture générale, vérification du dépôt Git, nettoyage du code et des fichiers inutiles | Dépôt final propre                  | Équipe                |
| Vendredi 12 juin — 10h à 12h | Livraison finale du projet et vérification des livrables                                  | Version finale prête à être évaluée | Équipe                |