# Manuel utilisateur — EventApp

Consultation des logs

---

## 1. Tableau de bord des logs — `/event`

Page principale affichant l'ensemble des logs de l'application.

### Statistiques globales (en haut de page)
- **Total logs** — nombre total de logs enregistrés
- **Quiz complétés** — nombre de questionnaires terminés
- **Connexions réussies** — nombre de connexions utilisateur réussies
- **Tentatives échouées** — nombre d'échecs de connexion
- **Score moyen** — moyenne des résultats au questionnaire (en pourcentage et sur 20)

### Graphiques
- **Répartition par priorité** — diagramme montrant la distribution des logs par niveau de sévérité (info, notice, warning, error…)
- **Répartition par type** — diagramme montrant la distribution par type d'événement (auth, quiz, system, question…)
- **Logs par jour** — histogramme des 7 derniers jours

### Filtres
- **Par type** : cliquer sur un type (`quiz`, `auth`, `system`, `question`, `manual`) pour ne voir que les logs de cette catégorie
- **Par priorité** : utiliser le paramètre `?priority=` (info, notice, warning, error)
- **Par date** : utiliser les paramètres `?date_from=YYYY-MM-DD` et `?date_to=YYYY-MM-DD`
- **Réinitialiser** : cliquer sur « Tous » pour effacer les filtres

### Liste des logs
- Affichage paginé (20 logs par page)
- Chaque log affiche : une icône selon le type, un libellé d'action, la date/heure, le message, un badge de sévérité (couleur), l'équipement (facility), l'identifiant utilisateur si présent, et le score si applicable
- Navigation : liens « Précédent » et « Suivant », indicateur de page

### Actualisation automatique
La page se raffraîchit automatiquement toutes les 10 secondes pour afficher les nouveaux logs en temps réel.

---

## 2. Statistiques par question — `/questions-stats`

Page dédiée aux résultats du questionnaire, question par question.

### Indicateurs généraux
- **Nombre total de questions posées** — cumul sur l'ensemble des quiz
- **Pourcentage moyen de bonnes réponses** — coloré en vert (≥ 80 %), orange, ou rouge (< 50 %)

### Meilleure et moins bonne question
Encadrés vert et rouge présentant :
- Le texte de la question
- Le pourcentage de bonnes réponses
- Le nombre de réponses correctes / total
- Une barre de progression visuelle

### Liste complète des questions
Chaque question affiche :
- Son numéro
- Le texte de la question
- Le pourcentage de réussite (coloré)
- Le compteur correct / total
- Une barre de progression

### État vide
Si aucun quiz n'a encore été réalisé, le message « Aucune donnée de quiz disponible » s'affiche.
