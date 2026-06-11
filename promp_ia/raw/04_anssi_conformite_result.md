# Réponse — Analyse de conformité ANSSI

## Date
10/06/2026

## Réponse brute

```
Voici les 9 recommandations ANSSI principales (guide PA-012 v2.0) :

## R1 — Centralisation des logs
Tous les logs doivent être centralisés sur un serveur dédié (rsyslog).
Éviter le stockage local sur chaque machine.

## R2 — Horodatage
Chaque log doit inclure une date/heure fiable (NTP).
Format ISO 8601 recommandé.

## R3 — Intégrité
Les logs doivent être protégés contre toute modification.
Signature ou stockage en écriture seule (append-only).

## R4 — Confidentialité
Les logs peuvent contenir des données sensibles.
Chiffrement en transit (TLS) et au repos.

## R5 — Conservation
Durées recommandées :
- Événements d'authentification : 6 mois
- Actions sensibles : 1 an
- Accès aux ressources : 1 an
- Activité process/système : 6 mois

## R6 — Événements à journaliser
- Authentification (succès/échec)
- Gestion des comptes (création/suppression/modification)
- Accès aux ressources sensibles
- Actions d'administration
- Activité système (démarrage/arrêt)

## R7 — Niveaux de gravité
INFO : événement normal
WARNING : événement anormal non bloquant
ERROR : erreur technique

## R8 — Séparation des responsabilités
Les logs ne doivent pas être modifiables par les utilisateurs
qui génèrent les événements.

## R9 — Revue régulière
Les logs doivent être consultés et analysés périodiquement
pour détecter des anomalies.
```
