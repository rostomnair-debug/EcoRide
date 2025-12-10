# Gestion de projet (Kanban)

Colonnes suggérées :
- Backlog (toutes les fonctionnalités priorisées)
- À faire (sprint en cours)
- En cours
- En revue/test
- Terminé (branche dev)
- Merge principal (fusion main)

Principales cartes (US) :
- US1 Accueil
- US2 Menu
- US3 Liste covoiturages + recherche
- US4 Filtres/tri
- US5 Détail trajet
- US6 Participation + crédits
- US7 Création compte
- US8 Espace utilisateur (passager/chauffeur)
- US9 Saisie voyage
- US10 Historique
- US11 Lifecycle trajet
- US12 Espace employé (avis/signalements)
- US13 Admin (stats, suspensions, employés)

Branches :
- main (prod)
- develop (intégration)
- feature/* (une fonctionnalité = une branche, merge en develop après test, puis release vers main)

Outils : Trello/Notion/Jira avec étiquettes (frontend, backend, mail, admin, employé, sécurité, docs). Consigner les tests et les validations avant merge.
