# Documentation technique

## Stack
- Backend : PHP 8 / Symfony, Twig, Doctrine ORM.
- Front : Twig + Bootstrap, Bootstrap Icons, JS pour interactions (modales, loaders, pagination).
- Mail : Mailpit en dev (SMTP 1025 / UI 8025).
- BDD : MySQL/MariaDB (Doctrine), tables principales : utilisateur, voiture, marque, covoiturage, avis, pivot covoiturage_participant.

## Modèle / données
- `utilisateur` : pseudo, nom, prenom, email, password, role (user/employe/admin), slug, crédits, vérifié, suspended, note_moyenne, created_at, date_naissance, photo.
- `voiture` : marque, modele, immatriculation, energie, couleur, date_premiere_immatriculation, propriétaire (+ nom/prénom/pseudo recopiés), created_at.
- `covoiturage` : slug, lieux, dates/heures départ/arrivée, nb_place, prix_personne, statut (à venir/en cours/terminé/annulé), commission, signale/motif, conducteur nom/prénom/pseudo, point_rdv/arrivee, created_at.
- `avis` : note, commentaire, statut (en attente/valide/refuse), signale/motif, auteur nom/prénom/pseudo, **rated_user_id** (cible notée), created_at.
- `covoiturage_participant` : copie nom/prénom/pseudo participant, lien utilisateur/covoiturage.
- `support_message` : messages du formulaire contact, consultés/répondus par un employé (markdown possible).

## Routes principales (extraits)
- Auth : `/login`, `/logout`, `/reset-password`, `/verify-email`.
- Profil : `/mon-espace`, `/credits/topup`, `/mon-espace-chauffeur`, `/form-chauffeur`.
- Covoiturage : `/` (home), `/covoiturages` (liste + filtres + tri + pagination), `/publish-ride`, `/covoiturage/{slug}` (détail), participation/avis/signalement/start/finish/cancel.
- Admin : `/admin-dashboard`, actions suspendre/promouvoir.
- Employé : `/espace-employe`, actions valider/refuser avis, traiter signalements, répondre au support.
- Contact : `/contact`.
- Profil public conducteur : `/conducteur/{slug}`.

## Logique métier clé
- Publication trajet : chauffeur connecté avec au moins un véhicule, commission 2 crédits, slug unique.
- Participation : modal confirmation, débit crédits passager, crédit conducteur (prix-commission), mise à jour places, mail de confirmation.
- Annulation : remboursement participants, retrait gain conducteur, mail d’annulation.
- Fin de trajet : statut terminé, mail aux passagers pour noter.
- Recharge crédits : modal fictive, MAJ solde + mail recap.
- Avis : passagers notent conducteur, conducteur note passagers (après trajet terminé), statut en attente, validation employé, affichage sur profil (moyenne calculée sur avis reçus).
- Signalements : trajets/avis signalés, traitement par employé.
- Support : messages contact enregistrés, consultables/répondus depuis l’espace employé (markdown → HTML).

## Filtres/tri/pagination
- Filtres : départ/arrivée, date, prix max, durée max, éco (énergie électrique/hybride), note min.
- Tri : date ou prix, ordre asc/desc, appliqué aux résultats et pagination (6 par page) ; suggestions avant/après si aucun résultat à la date.
- Admin/Employé : toutes les listes paginées (6) avec tri côté admin, tri à brancher côté employé selon besoin.

## UX tableaux / stats
- Admin : cartes de stats, graphiques et liste des covoiturages récents sont dans des collapses. Le graphique se rend lors de l’ouverture (Chart.js depuis `public/vendor/chartjs/chart.umd.js`), chaque ouverture détruit/recrée le canvas pour éviter les plantages de navigation.
- Employé : toutes les sections (utilisateurs, avis en attente/traités, signalements, support) sont repliables pour alléger l’affichage mobile. Boutons “Afficher/Masquer” se synchronisent sur l’état du collapse.

## Formats et i18n
- Dates affichées au format FR (`d/m/Y`, `H:i`), fuseau Paris à maintenir.
- Messages d’erreur login non indicatifs.

## Déploiement (détail)
1. Configurer variables d’environnement (DATABASE_URL, MAILER_DSN, APP_ENV=prod, APP_SECRET).
2. Lancer migrations : `php bin/console doctrine:migrations:migrate -n`.
3. Vider le cache : `php bin/console cache:clear --env=prod`.
4. Configurer le serveur web vers `public/` (Apache/Nginx) ; activer HTTPS.
5. Configurer un SMTP de production (remplacer Mailpit).

## Diagrammes (sources .mmd à exporter en PDF)
- MCD/diagramme de classes : `docs/diagrams/mcd_ecoride.mmd` (à exporter en `mcd_ecoride.pdf`).
- Use cases : `docs/diagrams/use_cases_ecoride.mmd` couvrant création compte, publication trajet, participation, validation avis (employé), suspension compte (admin) → à exporter en PDF.
- Séquence type : `docs/diagrams/sequence_reservation.mmd` retraçant réservation (passager) → confirmation crédits → mail → à exporter en PDF.
- Génère les PDF depuis ces sources (ex. mmdc/mermaid-cli ou draw.io) avant livraison finale.
