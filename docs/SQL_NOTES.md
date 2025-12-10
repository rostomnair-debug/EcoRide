# Scripts SQL / Migrations

Le projet s’appuie sur les migrations Doctrine. Commandes :
```
php bin/console doctrine:migrations:migrate -n
```

Tables clés (résumé) :
- `utilisateur` (pseudo, email, password, role, slug, credits, verifie, suspended, note_moyenne, created_at, date_naissance, photo).
- `voiture` (marque_id, modele, immatriculation, energie, couleur, date_premiere_immatriculation, proprietaire_id + copies nom/prenom/pseudo, created_at).
- `covoiturage` (slug, lieu_depart/arrivee, dates/heures, nb_place, prix_personne, statut, commission, conducteur_nom/prenom/pseudo, signale/motif, created_at).
- `avis` (note, commentaire, statut, signale/motif, auteur_nom/prenom/pseudo, rated_user_id, created_at).
- `covoiturage_participant` (utilisateur_id, covoiturage_id, participant_nom/prenom/pseudo).
- `marque`.

Si besoin d’un script SQL complet, exporter à partir de la BDD locale ou générer via `mysqldump`. Les migrations existantes couvrent l’ajout des colonnes `created_at`, la colonne `role` sur utilisateur, la suppression de l’ancienne table user, et les nouvelles colonnes pour noms/pseudos duplicables.
