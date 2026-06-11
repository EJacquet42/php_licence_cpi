## Gestion des risques

| Risque                                     | Impact                                       | Mesure prévue                                                                   |
| ------------------------------------------ | -------------------------------------------- | ------------------------------------------------------------------------------- |
| Les logs ne sont pas générés               | Impossible de tracer les actions utilisateur | Tester chaque événement critique un par un                                      |
| Les logs contiennent des données sensibles | Risque de sécurité                           | Ne jamais enregistrer les mots de passe                                         |
| Mauvaise configuration rsyslog             | Les logs ne sont pas centralisés             | Vérifier la configuration avec des logs de test                                 |
| Erreur lors du rendu du quiz               | Perte de données ou absence de trace         | Ajouter un log d’erreur en cas d’échec                                          |
| Documentation incomplète                   | Projet difficile à installer ou maintenir    | Documenter l’installation, les ports et les services                            |
| Retard de développement                    | Fonctionnalités non finalisées               | Prioriser les logs essentiels : inscription, connexion, déconnexion, rendu quiz |