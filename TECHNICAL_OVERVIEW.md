# EcoRide – Synthèse Technique

Ce document résume l'état actuel du prototype front-end statique, les comportements implémentés en JavaScript, ainsi que les ajustements récents appliqués pour corriger les graphiques du tableau de bord administrateur.

## 1. Structure du projet en un coup d'œil
- **Pages HTML** : maquettes des principaux parcours utilisateurs (page d'accueil, liste des trajets, fiches conducteurs, espaces d'authentification, panneau admin, etc.).
- **`css/style.css`** : feuille de styles partagée qui complète les composants Bootstrap 5 chargés via CDN.
- **Modules JavaScript (`js/`)** : scripts natifs gérant le peuplement d'éléments d'interface, les jeux de données simulés et la logique des tableaux de bord/validations.
- **Assets** : `img/` et `assets/` regroupent les visuels et logos du site ; `pdf/` contient des documents annexes pour illustrer les téléchargements.

## 2. Pages clés et objectif
- `index.html` : page d'accueil avec un formulaire de recherche de trajets redirigeant vers la page de résultats.
- `covoiturages.html` : affiche des offres simulées et lit les paramètres d'URL (`depart`, `arrivee`, `date`) produits par le formulaire d'accueil.
- `detail_*.html` : profils conducteurs associés aux cartes de trajet.
- `my_space*.html`, `employee_space.html` : maquettes d'espaces comptes selon le rôle utilisateur.
- `admin_dashboard.html` : tableau de bord administrateur alimenté par Chart.js.
- `contact.html`, `publish_ride.html`, `form_chauffeur.html`, `login.html`, `sign_in.html` : formulaires et parcours complémentaires du prototype.

## 3. Modules JavaScript et fonctions

### `js/script.js`
- **Jeu de données** `villesFrancaises` : liste d'objets `{ nom, code }` pour remplir les sélecteurs de départ/arrivée.
- **`remplirSelecteursVilles()`** : injecte des `<option>` dans les contrôles `#depart` et `#arrivee` si le DOM les expose.
- **`brancherFormulaire()`** : attache un gestionnaire `submit` au premier `<form>` de la page, vérifie les champs vides et redirige vers `covoiturages.html` avec des paramètres encodés.
- Les deux fonctions sont initialisées sur `DOMContentLoaded` pour garder le formulaire autonome.

### `js/villes.js`
- Empêche la sélection d'une même ville en départ et arrivée en désactivant l'option correspondante dans l'autre liste déroulante.
- Des écouteurs sur les deux `<select>` réagissent aux changements et réinitialisent l'autre champ si nécessaire.

### `js/trajets.js`
- Définit `conducteursEtTrajets`, un tableau d'objets conducteurs avec coordonnées, véhicule et une liste `trajets` imbriquée.
- Ce jeu de données sert de base à `covoiturages.html` (via scripts inline) pour la génération, le filtrage et la navigation vers les fiches conducteurs.

### `js/admin_dashboard.js`
- Initialise trois visualisations Chart.js (barres, donut, courbe) pour le tableau de bord administrateur.
- Renforcements récents :
  - Vérifie la présence des canvases et de `window.Chart` avant instanciation.
  - Remplace le canvas par une alerte si Chart.js est indisponible (`data-chart-fallback`).
  - Encapsule chaque canvas dans `.chart-container` pour empêcher l'étirement infini et stabiliser le layout.
  - Désactive les animations et applique `resizeDelay` pour limiter les re-rendus.

### `js/employee.js`
- Simule les files de modération destinées aux employés.
- **`afficherAvisEnAttente()`** : génère des cartes pour les avis en attente et relie les boutons « valider » / « refuser ».
- **`prendreDecisionSurAvis(id, decision)`** : retire un avis de la file et consigne la décision.
- **`ajouterHistoriqueDecision(avis, decision)`** : ajoute en tête de liste un résumé marqué d'un badge selon la décision.
- **`afficherIncidents()`** : liste les incidents signalés avec leur contexte.
- Les deux rendus sont lancés sur `DOMContentLoaded`.

### `js/test.js`
- Conserve un ancien bloc de code commenté et gère la validation du formulaire d'inscription :
  - Valide email et mot de passe via expressions régulières.
  - Vérifie la concordance du champ de confirmation.
  - Applique les classes Bootstrap `is-valid` / `is-invalid` en temps réel et à la soumission.
- S'exécute uniquement si tous les champs attendus (`#EmailInput`, `#PasswordInput`, `#ValidatePasswordInput`, `#form`) sont présents.

### Remarques complémentaires
- `js/trajet_backup.js` contient un dataset plus riche (commenté) pouvant servir de base à des scénarios avancés.
- Certains fichiers HTML (notamment `covoiturages.html`) incluent des scripts inline qui exploitent `conducteursEtTrajets` pour construire l'interface au chargement.

## 4. Styles et mise en page
- `.chart-container` et `.chart-container--wide` (dans `css/style.css`) fixent une hauteur intrinsèque et forcent le canvas à occuper l'espace nécessaire sans dériver.
- Les personnalisations du menu et du carrousel héros cohabitent avec ces helpers ; Bootstrap reste responsable de la majorité de la mise en page.

## 5. Corrections récentes des graphiques
1. **Chargement différé de Chart.js** pour ne plus bloquer l'exécution et garantir la disponibilité de la librairie avant l'initialisation (`<script defer src="https://cdn…chart.umd.min.js"></script>`).
2. **Fallback informatif** quand un canvas ou Chart.js manque, via remplacement par une alerte lisible.
3. **Stabilisation du layout** grâce aux conteneurs dédiés et aux garde-fous CSS, évitant l'étirement infini qui faisait planter la page.
4. **Réduction de la pression de rendu** en désactivant les animations et en retardant les recalculs via `resizeDelay`.

## 6. Travailler avec le prototype
- Ouvrir `index.html` directement ou servir le répertoire `ECF_Rostom` via un serveur statique pour conserver les chemins relatifs.
- Pour intégrer des données réelles, enrichir `conducteursEtTrajets` ou le remplacer par des appels API (XHR/Fetch) dès qu'un backend sera disponible.
- Lorsque de nouveaux scripts sont ajoutés, vérifier la présence des éléments ciblés avant de brancher les écouteurs afin d'éviter les erreurs sur les pages non concernées.

Compléter cette synthèse au fur et à mesure que de nouveaux parcours ou jeux de données rejoignent le prototype.
