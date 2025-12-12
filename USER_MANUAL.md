# Manuel d’utilisation (résumé)

## Rôles et comptes de test
- Admin : email ross@mail.com / Ross123! (ROLE_ADMIN)
- Employé : email sergioramos@mail.com / Ramos123! (ROLE_EMPLOYE)
- Utilisateur : créer un compte via « Créer un compte » (20 crédits offerts)

## Parcours utilisateur
1. Créer un compte (pseudo, mail, mot de passe). Vérifier l’email (Mailpit en dev).
2. Se connecter, accéder à « Mon espace » : éditer profil, ajouter véhicule, recharger crédits.
3. Rechercher un trajet via « Covoiturages » : filtres, tri, éco, pagination.
4. Détail trajet : consulter infos conducteur, voiture, badges éco/CO₂, avis, réserver via modal de confirmation (débit crédits, mail de confirmation).
5. Mes trajets : voir passés/en cours/à venir, signaler ou évaluer après le trajet, annuler une réservation (commission non remboursée).
6. Profil (public) : note moyenne affichée si avis reçus.

## Parcours chauffeur
1. Ajouter un véhicule dans Mon espace.
2. Publier un trajet via « Publier un trajet » (si connecté + véhicule). Commission fixe 2 crédits.
3. Démarrer / terminer / annuler depuis la page détail. Fin de trajet envoie un mail de demande d’avis aux passagers. Peut noter ses passagers.

## Parcours employé
- Accéder à « Espace Employé » (ROLE_EMPLOYE) : valider/refuser avis, traiter signalements trajets/avis (listes paginées), répondre aux messages support (éditeur markdown). Toutes les sections sont repliables pour un meilleur confort mobile.

## Parcours admin
- Accéder à « Dashboard Admin » (ROLE_ADMIN) : stats 7j (covoits/jour, revenus crédits), gestion utilisateurs (suspension, promotion employé), liste covoiturages récents (paginés, triables). Les blocs de stats et la liste des covoiturages se déplient à la demande pour limiter le chargement initial.

## Mails (dev Mailpit)
- Confirmation réservation, recharge crédits, contact support, demande d’avis après trajet, reset password, vérification email. MAILER_DSN pointant vers Mailpit.

## Raccourcis pages
- Accueil `/`
- Liste covoiturages `/covoiturages`
- Publier `/publish-ride`
- Mon espace `/mon-espace`
- Admin `/admin-dashboard`
- Employé `/espace-employe`
- Contact `/contact`
