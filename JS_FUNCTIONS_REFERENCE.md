# EcoRide – Référentiel des fonctions JavaScript

Ce document détaille chaque fonction définie dans les scripts du dossier `js/` et précise leur rôle, leurs paramètres et leur valeur de retour lorsqu'il y en a une.

## `js/admin_dashboard.js`
- **`document.addEventListener('DOMContentLoaded', function () { … })`**
  - Point d'entrée principal. Récupère les trois canvases de la page admin, vérifie leur présence ainsi que celle de `window.Chart`, instancie les graphiques si possible ou déclenche les fallbacks sinon.
- **`chartCanvases.some(function (canvas) { … })`**
  - Fonction de prédicat passée à `Array.prototype.some`. Retourne `true` si l'un des éléments récupérés n'est pas un `<canvas>` valide, ce qui provoque l'arrêt de l'initialisation.
- **`new Chart(… , { … })`**
  - Trois instanciations successives (barres, donut, courbe). Chaque appel configure type, jeu de données, options responsives, désactive les animations et applique `resizeDelay` pour limiter les recalculs.
- **`showChartFallback(canvas)`**
  - Remplace un canvas donné par une alerte Bootstrap contenant le message défini dans l’attribut `data-chart-fallback`. Utilisé lorsque Chart.js est absent.

## `js/script.js`
- **`remplirSelecteursVilles()`**
  - Paramètres : aucun. Parcourt le tableau global `villesFrancaises`, crée des `<option>` pour chaque ville et les ajoute aux sélecteurs `#depart` et `#arrivee`. Retour : `void`.
- **`brancherFormulaire()`**
  - Paramètres : aucun. Récupère le premier formulaire présent, intercepte la soumission, vérifie que départ/arrivée/date sont renseignés et redirige vers `covoiturages.html` en ajoutant les paramètres encodés dans l’URL. Retour : `void`.
- **`document.addEventListener('DOMContentLoaded', function () { … })`**
  - Initialise la page d’accueil en appelant `remplirSelecteursVilles()` puis `brancherFormulaire()` une fois le DOM prêt.

## `js/villes.js`
- **`document.addEventListener('DOMContentLoaded', function () { … })`**
  - Attache deux gestionnaires aux sélecteurs `#depart` et `#arrivee` afin d’empêcher la sélection de la même ville aux deux extrémités d’un trajet.
- **`departSelect.addEventListener('change', function () { … })`**
  - Désactive dans la liste d’arrivée l’option correspondant à la ville qui vient d’être choisie comme départ et réinitialise l’arrivée si nécessaire.
- **`arriveeSelect.addEventListener('change', function () { … })`**
  - Effet miroir : désactive dans la liste de départ la ville sélectionnée comme arrivée et remet à zéro le départ lorsqu’un doublon est détecté.

## `js/trajets.js`
- **`calculerDureeMinutes(heureDepart, heureArrivee)`**
  - Paramètres : deux chaînes au format `HH:MM`. Convertit les heures en minutes, gère les trajets franchissant minuit et renvoie la durée en minutes (`Number`). Retourne `NaN` si les entrées sont invalides.
- **`creerCarteTrajet(conducteur, trajet, accent = false, isSuggestion = false)`**
  - Produit dynamiquement un élément `div` contenant la carte Bootstrap d’un trajet, complète les attributs `dataset` pour les filtres et signale le mode suggestion si nécessaire. Retour : `HTMLElement`.
- **`afficherTousLesTrajets(container)`**
  - Vide le conteneur passé en argument puis y injecte toutes les cartes de trajets en utilisant `creerCarteTrajet` pour chaque combinaison conducteur/trajet. Retour : `void`.
- **`formatDateFr(isoDate)`**
  - Convertit une date ISO en libellé localisé (`fr-FR`). Retourne la chaîne initiale si la conversion échoue.
- **`trouverDatesAlternatives(depart, arrivee, dateRecherche)`**
  - Cherche des trajets correspondant au même couple `depart/arrivee`. Renvoie `null` si aucun résultat ; sinon renvoie un objet `{ date, trajets }` proposant la prochaine date disponible et les trajets associés.
- **`supprimerMessagesEtSuggestions(container)`**
  - Supprime du conteneur tous les messages d’information et les cartes marquées comme suggestions. Retour : `void`.
- **`appliquerFiltres(container, filtres)`**
  - Applique l’ensemble des filtres (écologique, prix, note, places, date, durée) aux cartes présentes dans `container`. Affiche un message ou des suggestions si aucun trajet ne correspond. Retour : `void`.
- **`initialiserPageTrajets()`**
  - Point d’entrée de `covoiturages.html`. Prépare le conteneur, lit les paramètres d’URL, met à jour le titre, initialise les filtres et branche les écouteurs qui rappellent `appliquerFiltres`.
- **`document.addEventListener('DOMContentLoaded', function () { … })`**
  - Lance `initialiserPageTrajets()` une fois que la page résultats est prête.

## `js/employee.js`
- **`afficherAvisEnAttente()`**
  - Nettoie la zone `#liste-avis`, affiche chaque avis en attente sous forme de carte et branche les boutons de validation/refus via des closures qui appellent `prendreDecisionSurAvis` avec l’ID ciblé.
- **`prendreDecisionSurAvis(idAvis, decision)`**
  - Retire l’avis correspondant du tableau `avisEnAttente`, puis appelle `ajouterHistoriqueDecision` avant de relancer `afficherAvisEnAttente` pour rafraîchir la liste.
- **`ajouterHistoriqueDecision(avis, decision)`**
  - Ajoute un élément à la liste `#historique-decisions` avec badge « validé » ou « refusé ». Efface le message par défaut lors de la première insertion.
- **`afficherIncidents()`**
  - Réinitialise le conteneur `#liste-incidents` et génère des cartes détaillant chaque incident présent dans `incidentsSignales` (ou un message de succès s’il n’y en a pas).
- **`document.addEventListener('DOMContentLoaded', function () { … })`**
  - Déclenche immédiatement `afficherAvisEnAttente()` et `afficherIncidents()` à l’ouverture de la page employé.

## `js/test.js`
- **`document.addEventListener('DOMContentLoaded', function () { … })`**
  - Ne s’exécute que si les champs d’inscription attendus sont trouvés. Définit les regex d’email/mot de passe, attache les validateurs et intercepte la soumission du formulaire.
- **`validateEmail()`**
  - Vérifie la saisie de `emailInput` à l’aide d’une expression régulière. Retourne `true` si l’email est conforme, `false` sinon, tout en appliquant les classes Bootstrap de validation.
- **`validatePassword()`**
  - Vérifie `passwordInput` selon la politique (8–20 caractères, majuscule, minuscule, chiffre, caractère spécial). Retourne un booléen et met à jour les classes de feedback.
- **`validatePasswordConfirm()`**
  - S’assure que le mot de passe confirmé correspond au mot de passe principal et que ce dernier est valide. Retourne un booléen et applique les classes visuelles.
- **`form.addEventListener('submit', function(event) { … })`**
  - Empêche la soumission si l’une des validations échoue et déclenche `validateEmail`, `validatePassword`, `validatePasswordConfirm` pour afficher les retours utilisateur.

## `js/admin_dashboard.js` – notes additionnelles
Les trois instances `new Chart` créent respectivement :
1. un histogramme des trajets par ville,
2. un diagramme en anneau pour la répartition écologique,
3. une courbe illustrant la croissance mensuelle des inscriptions.
Chaque configuration partage des options communes (`responsive`, `maintainAspectRatio: false`, `resizeDelay: 200`) pour éviter les re-rendus excessifs.

## `js/trajet_backup.js`
Ce fichier contient plusieurs blocs commentés conservant d’anciennes versions du dataset et des fonctions utilitaires (`afficherTrajets`, `participerAuTrajet`, gestion des filtres, etc.). Elles servent de référence historique mais ne sont pas exécutées dans la version actuelle du prototype.
