# EcoRide

EcoRide est un prototype front-end d'un service de covoiturage écologique réalisé dans le cadre d'un projet pratique Formation Studi. Le dépôt rassemble des pages statiques, des ressources et des scripts JavaScript qui illustrent les principaux parcours utilisateurs du produit.

## Vue d'ensemble du projet
- Page d'accueil (`index.html`) avec un formulaire de recherche de trajets relié à la page de résultats.
- Liste des trajets (`covoiturages.html`) qui lit des données simulées pour afficher les trajets à venir et les informations des conducteurs.
- Pages de détail conducteur (`detail_*.html`) présentant le profil, le véhicule et quelques avis.
- Espaces compte pour passagers, chauffeurs et employés (`my_space*.html`, `employee_space.html`) ainsi que des maquettes d'authentification (`login.html`, `sign_in.html`).
- Pages d'assistance comme les formulaires de contact, la publication de trajets et les téléchargements de documentation.

## Technologies utilisées
- HTML5 avec Bootstrap 5 fourni via CDN.
- Styles personnalisés regroupés dans `css/style.css` et classes spécifiques directement dans les pages.
- Modules JavaScript natifs pour les comportements (`js/script.js`, `js/villes.js`, `js/trajets.js`, `js/employee.js`).
- Ressources statiques dans `img/` et `assets/`, avec des documents complémentaires dans `pdf/`.

## Structure du dépôt
- `index.html` – point d'entrée de la page d'accueil.
- `covoiturages.html` – résultats de recherche alimentés par `js/trajets.js`.
- `detail_*.html` – fiches conducteurs accessibles depuis la page des résultats.
- `publish_ride.html`, `form_chauffeur.html` – parcours de soumission pour les nouveaux conducteurs et trajets.
- `my_space.html`, `my_space_chauffeur.html`, `employee_space.html` – maquettes d'espaces comptes.
- `js/`, `css/`, `img/`, `assets/`, `pdf/` – code et médias de support pour l'interface.

## Prise en main
1. Clonez ou téléchargez ce dépôt.
2. Servez le dossier avec un serveur de fichiers statiques (par exemple `php -S 127.0.0.1:8080`) ou ouvrez directement `index.html` dans un navigateur.
3. Conservez la structure des dossiers afin que les chemins relatifs vers les styles, scripts et médias restent valides.

## Données et scripts
- `js/trajets.js` initialise l'application avec des métadonnées de conducteurs et de trajets utilisées sur les pages de résultats et de détail.
- `js/script.js` et `js/villes.js` remplissent le formulaire de recherche, gèrent la validation et redirigent l'utilisateur vers les résultats correspondants.
- `js/employee.js` affiche les files de modération et les incidents dans les maquettes du tableau de bord employé.
- Vous pouvez ajouter d'autres scripts ou jeux de données dans le répertoire `js/` et les référencer depuis la page HTML concernée.

## Conseils de développement
- La mise en page partagée repose sur les composants Bootstrap ; privilégiez `css/style.css` pour les modifications communes au site.
- Il n'y a ni pipeline de build ni gestionnaire de dépendances : modifiez les fichiers statiques puis rechargez votre navigateur pour tester.
- Le dossier `php/` contient pour l'instant des éléments factices ; vous pouvez l'étendre en véritable backend si le prototype évolue.

## Pistes d'amélioration
- Remplacer les tableaux codés en dur par des données récupérées via une API REST ou un fichier JSON.
- Ajouter une validation côté client, des audits d'accessibilité et des tests automatisés à mesure que le prototype grandit.
- Introduire un bundler, du linting ou un système de composants si le front-end devient plus complexe.



https://mermaid.live/edit#pako:eNqtV_9u6jYUfhUr0pU2rTDKXWmLtEkUzL25TQNLodOdkCI3dsFaYke2Qy9r-jy7fQ5ebCeBQKBpR7X2jxKdc-xjf9_55QcrkJRZbevDB4QD6XHKUA1ddXvoB-z9OBEg7miTBKyNKBEaXTEVEU6Rw-fsCEXMaDSx-qG8D2ZEGeR4E2tlGDKNNDOGiyl8cES5jmV9IpjqcTJVJJoIBH_ja-yhNK3V5AO6wZ_troNRG7aMpdbL75RNrOd2I88e5kaBFDThprDJ5Wubi8Hg0nY_5WZq-aSZmhPDpdBVG1YYs51NH2WtlqY7B0wMD7muPF_Xwz175I-8jnvd6Y7sgZuviGQyZxETpvIMHr6x8R-54fIJcK26VMlGseU_cnt1fDV0Bl8xrrCMJF1-V5UHtd2u3cPuKLfTfCpIyKr87tgB6AFTgogXne-YG0W4qfQ-9HAfe9jtrgCly6c7LlZXKhk_rL4RuuVTLgyC2BteFrI5UVncoVizhErYZOzav49x4W1rwCBow1f0MdH6Xirqz4iebTxKGTIiENc-VRDvqkKRLWRiWqljURzKBWMVKkIjLgp5dq1AMcrzZKLs7tdmY3tEygIekRCR-dRXJMsnMHLHjrM1KTbWiY6ZoIyut7kj4TZCETI8YtqQKM68EcOoT8xzXRLTHd1jQUcR_a8yspbJe0h0HzT9SzhL7becyzqnFciH4K2CmewQ6I4rbXzFplwblefv_vJbRQTdF0LIs3BfGMhQblhiIongH1PTBfjGDu5CvHfTz18vPLuXfupcDxzbxWnPxte4AuiM3JAFRvFgFbkQHsunQ5DeoJnn1yFQrkLvVSzXlnM240HIyqZrziqRlypb5gfcLF5QEUoV03pfS-FuXOR0VK4u6_e2yFjNwAGbGPpFolgpBjdKouDOJPSZIeUcMdKAUAOguizOBX7I7sx-ysTAD_NZUpmcgdwJBqDLJFn-DZ2O6-Jeej3qeCP47Q6gvOH8qwO1ynFw701EF83lEK4hoOIyfVmQVDG9qTqvhsUGnLKAsltu_HW5eQEB7PbgxH5vML5wsN8duH3bu0rXv2UgUijhY7f3AiJSQP5GOSbPStYBwFW00UMwTPQBsBBoxiI71U8_17aaHAaziLN6ZLv2yO44_sXAHV-naxL9Hr6wRymEyKg_8K78PoYa4dk32PNxx3PXcDyjq6B1BUJ2sipuD4Bk3dbfM5QUm3N2f1iBOaAUQeIv8m2LTnVcr5-U1Owb9DoZZWPQDupZyV5V-G0App3h0Bvc5GH2BSo0fo4siWMl5wDV7WIX3WImeSvCm1nmLaFWdK-17wpQ_tPvZmZ6X25jqcz7cZuRB8U9APflXrxbPQbAXmq7WdkYQdoCd9BMXyVxBi08_P8cHjTElIbOdykmRUchIbyBtK8j-RfE_QtaIrK2pHeTIdFGRn7-_cYiORHWkTVVnFptoxJ2ZEWrxxm85vK7wfA9gwfHxGqv5kGShPl0_QjLYiL-lDIqViqZTGdWOx8Yj6wVeut32kaqstFSdbPCabXPP-Z7WO0H65vVPm7Um83T82azcfpLo_Xx-BS0C6tdO2nVj1vnrdb56Vmr2Wg0z08ej6y_c7_HdTA-O2uCfbMBv42TIyvrSVJdrR6k-bv08V92bodW