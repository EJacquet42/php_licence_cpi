# Synthèse ANSSI : Logs à garder ou non

Source : [ANSSI] Recommandations de sécurité pour l'architecture d'un système de journalisation (v2.0, 28/01/2022)

---

## 1. Logs à GARDER — Socle minimal (Annexe A)

| Domaine | Catégories d'évènements | Pourquoi |
|---|---|---|
| **Authentification** | Ouvertures de sessions, réussites/échecs d'authentification, utilisation de privilèges | Détecter les tentatives d'accès non autorisées, mouvements latéraux, escalade de privilèges |
| **Gestion des comptes** | Création de comptes/groupes/rôles, désactivation/verrouillage, octroi de privilèges, ajout dans des groupes, assignation de rôles, modification des secrets (mots de passe) | Détecter la persistance malveillante, création de backdoors, compromission de comptes |
| **Stratégies de sécurité** | Modification des paramétrages de sécurité, modification des stratégies d'audit, effacement de journaux | Détecter une tentative de dissimulation d'activité malveillante par un attaquant |
| **Accès aux ressources sensibles** | Accès ou tentatives d'accès en lecture/écriture/exécution/suppression ⚠️ volumétrie potentiellement forte | Détecter l'exfiltration de données et les accès non autorisés aux ressources critiques |
| **Activité des processus** | Démarrages/arrêts ⚠️, dysfonctionnements, chargements/déchargements de modules ⚠️, exécution de scripts ⚠️ | Détecter l'exécution de code malveillant, le chargement de drivers ou modules noyau suspects |
| **Activité des systèmes** | Démarrages/arrêts, dysfonctionnements/surcharges, chargements/déchargements de modules noyau, activité matérielle (défaillances, connexions/déconnexions physiques) | Détecter les tentatives de bootkit/rootkit, altération du noyau, ou sabotage matériel |

⚠️ = Attention volumétrie potentiellement forte — configurer avec précaution.

---

## 2. Logs à ÉVITER ou LIMITER

| Type | Recommandation | Pourquoi |
|---|---|---|
| **Mode debug / verbose** | Ne pas activer en production (R6 — Attention) | Génère un volume massif, peut révéler des secrets (mots de passe, clés) en clair, rend l'exploitation inefficace |
| **Données à caractère personnel** | Minimiser leur inclusion (Annexe D, RGPD/CNIL) | Obligation légale : une IP, URL, email sont considérées comme données personnelles par la CNIL |
| **Journaux métier contenant des données personnelles** | Prévoir un mécanisme de suppression automatique au-delà de la durée légale (section 2.1, R25) | Conformité RGPD — droit à l'oubli, proportionnalité de la conservation |
| **Évènements à très forte volumétrie** | Tester sur un périmètre réduit avant déploiement large (Annexe A) | Éviter la saturation des serveurs de collecte, la perte d'évènements critiques, et la pollution du SIEM |
| **Authentification en mode pull** | Compte de service dédié, privilèges réduits, mot de passe robuste (R15) | Limiter la surface d'attaque : un serveur de collecte compromis en mode pull donne accès à tous les équipements |
| **Journaux non horodatés** | Impossible de corréler les évènements (R3, R4) | Inexploitables pour la détection d'incidents et la reconstruction chronologique |

---

## 3. Durées de rétention (R25)

| Contexte | Durée recommandée | Base légale |
|---|---|---|
| Cas général | **6 mois à 1 an** | CNIL (délib. n°2021-122) |
| Cas particuliers justifiés | Jusqu'à **3 ans** | CNIL (obligation légale, sensibilité du traitement) |
| Attaque avérée ou suspectée | Au-delà de la durée réglementaire | CNIL point 20 — conservation prolongée justifiée |
| FAI / Hébergeurs | **3 mois à 1 an** (identité : jusqu'à 5 ans) | Code des postes et communications électroniques |
| Opérateurs de communications | **3 mois à 5 ans** selon le type de donnée | Art. L. 34-1 CPCE |

**Principe** : Suppression automatique dès que la durée de rétention est atteinte (R25).

---

## 4. Principes clés

1. **Progressivité** (Annexe A) : Commencer par un petit ensemble d'évènements bien maîtrisés, puis enrichir par itération. Ne pas viser l'exhaustivité immédiate.
2. **Exhaustivité du périmètre** (R2) : Journaliser un maximum d'équipements (postes, serveurs, réseaux, applis) mais avec une verbosité adaptée.
3. **Centralisation** (R9) : Tous les logs doivent converger vers un ou plusieurs serveurs centraux dédiés.
4. **Protection** (R26, R27) : Accès en écriture/lecture/suppression restreint au strict besoin opérationnel.
5. **Minimisation des données personnelles** (Annexe D) : Ne journaliser que ce qui est nécessaire à la finalité de sécurité.

---

*Document généré à partir du guide ANSSI-PA-012 (v2.0) — Licence Ouverte v2.0 (Etalab)*
