# EcoRide

[📖 Manuel utilisateur](USER_MANUAL.md)

Plateforme de covoiturage orientée écologie (Symfony/PHP, Twig, Doctrine).

## Prérequis
- PHP 8.2+
- Composer
- Node/NPM ou Yarn (pour les assets si besoin)
- Base de données MySQL/MariaDB
- Mailpit pour les mails locaux

## Installation locale
```bash
git clone <repo>
cd EcoRide
composer install
cp .env .env.local
# Ajuster DATABASE_URL et MAILER_DSN (ex: smtp://127.0.0.1:1025)
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n
# Normaliser les dates des trajets si besoin
# php bin/console app:covoiturage:normalize-dates
# (optionnel) php bin/console doctrine:fixtures:load -n
```

## Lancer l’application
```bash
symfony serve -d   # ou php -S localhost:8000 -t public
```

## Mailpit
- Installer et lancer Mailpit (ex: `mailpit --smtp 1025 --http 8025`).
- Configurer `MAILER_DSN=smtp://127.0.0.1:1025` dans `.env.local`.
- Boîte de réception accessible sur http://127.0.0.1:8025.

## Commandes utiles
- `php bin/console doctrine:migrations:migrate` : appliquer les migrations.
- `php bin/console app:covoiturage:normalize-dates` : recalcule dates/horaires trajets (hors en cours/terminés).
- Migrations notables : `rated_user_id` sur `avis`, `note_moyenne` sur `utilisateur`.
- `php bin/console cache:clear` : vider le cache.
- `php bin/console debug:router` : vérifier les routes.

## Structure des contrôleurs
- AuthController : login/reset/vérif email.
- ProfileController : espace perso, véhicules, crédits.
- CovoiturageController : home, liste/filtre/tri, détail, publication, participation/annulation, avis (note conducteur/passagers), signalement, lifecycle.
- AdminController : dashboard (Chart.js local), gestion utilisateurs/employés.
- EmployeeController : validation/refus avis, signalements, support messages.
- ContactController : formulaire contact/support (Mailpit).
- UserController : profil public conducteur (note moyenne, avis reçus).

## Sécurité / voters
- `CovoiturageVoter::MANAGE` : seul le conducteur (ou un admin) peut modifier/annuler ses trajets.
- `AvisVoter::MODERATE` : modération des avis/signalements réservée aux rôles admin et employé.
- `UtilisateurVoter::SUSPEND` / `PROMOTE` : suspension/promotion de comptes réservées aux admins.

## Tests rapides
- Vérifier les pages `/`, `/covoiturages`, `/mon-espace`, `/admin-dashboard` (ROLE_ADMIN), `/espace-employe` (ROLE_EMPLOYE).
- Réservation : modal de confirmation + mail, décrément/crédit OK, annulation par passager (commission non remboursée).
- Recharge crédits : modal fictive + mise à jour solde + mail.
- Notes : conducteur/passagers peuvent être notés après trajet terminé, moyenne affichée sur profils/cartes.

## Déploiement (résumé)
- Définir variables d’env (DATABASE_URL, MAILER_DSN production).
- Exécuter migrations, vider cache, configurer serveur web vers `public/`.
