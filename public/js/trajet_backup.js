
/*

const conducteursEtTrajets = [
  {
    "conducteur": {
      "id": 101,
      "pseudo": "JeanD",
      "nom": "Jean Dupont",
      "photo": "images/avatars/jean_dupont.jpg",
      "note": 4.5,
      "email": "jean.dupont@example.com",
      "telephone": "0612345678",
      "vehicules": [
        {
          "id": 201,
          "modele": "Tesla Model 3",
          "marque": "Tesla",
          "couleur": "Blanc",
          "energie": "Électrique",
          "immatriculation": "AB-123-CD",
          "places": 4
        },
        {
          "id": 202,
          "modele": "Renault Zoé",
          "marque": "Renault",
          "couleur": "Bleu",
          "energie": "Électrique",
          "immatriculation": "CD-456-EF",
          "places": 5
        }
      ],
      "preferences": {
        "fumeur": false,
        "animaux": true,
        "musique": "Jazz/Classique",
        "autres": "Préfère les discussions légères"
      }
    },
    "trajets": [
      {
        "id": 1,
        "depart": "Paris",
        "arrivee": "Lyon",
        "date": "2025-10-15",
        "heureDepart": "10:00",
        "heureArrivee": "12:30",
        "prix": 15,
        "placesDisponibles": 2,
        "ecologique": true,
        "vehiculeId": 201,
        "avis": [
          {
            "id": 1001,
            "passager": "PaulM",
            "note": 5,
            "commentaire": "Très ponctuel et agréable !",
            "date": "2025-10-10"
          },
          {
            "id": 1002,
            "passager": "SophieL",
            "note": 4,
            "commentaire": "Conducteur prudent, voyage confortable.",
            "date": "2025-09-28"
          }
        ]
      },
      {
        "id": 2,
        "depart": "Lyon",
        "arrivee": "Marseille",
        "date": "2025-10-20",
        "heureDepart": "14:00",
        "heureArrivee": "17:30",
        "prix": 20,
        "placesDisponibles": 3,
        "ecologique": true,
        "vehiculeId": 202,
        "avis": [
          {
            "id": 1003,
            "passager": "MarcT",
            "note": 5,
            "commentaire": "Super trajet, voiture très propre.",
            "date": "2025-10-12"
          }
        ]
      }
    ]
  },
  {
    "conducteur": {
      "id": 102,
      "pseudo": "MarieL",
      "nom": "Marie Lambert",
      "photo": "images/avatars/marie_lambert.jpg",
      "note": 4.8,
      "email": "marie.lambert@example.com",
      "telephone": "0623456789",
      "vehicules": [
        {
          "id": 203,
          "modele": "Peugeot 3008",
          "marque": "Peugeot",
          "couleur": "Noir",
          "energie": "Diesel",
          "immatriculation": "EF-789-GH",
          "places": 5
        }
      ],
      "preferences": {
        "fumeur": false,
        "animaux": false,
        "musique": "Variété française",
        "autres": "Préfère les trajets silencieux"
      }
    },
    "trajets": [
      {
        "id": 3,
        "depart": "Bordeaux",
        "arrivee": "Toulouse",
        "date": "2025-10-18",
        "heureDepart": "08:30",
        "heureArrivee": "11:00",
        "prix": 12,
        "placesDisponibles": 1,
        "ecologique": false,
        "vehiculeId": 203,
        "avis": [
          {
            "id": 1004,
            "passager": "LucieP",
            "note": 5,
            "commentaire": "Très professionnelle, trajet très agréable.",
            "date": "2025-10-05"
          }
        ]
      }
    ]
  },
  {
    "conducteur": {
      "id": 103,
      "pseudo": "PierreT",
      "nom": "Pierre Tremblay",
      "photo": "images/avatars/pierre_tremblay.jpg",
      "note": 4.2,
      "email": "pierre.tremblay@example.com",
      "telephone": "0634567890",
      "vehicules": [
        {
          "id": 204,
          "modele": "Citroën C4",
          "marque": "Citroën",
          "couleur": "Gris",
          "energie": "Essence",
          "immatriculation": "GH-123-IJ",
          "places": 4
        }
      ],
      "preferences": {
        "fumeur": true,
        "animaux": true,
        "musique": "Rock",
        "autres": "Aime discuter pendant le trajet"
      }
    },
    "trajets": [
      {
        "id": 4,
        "depart": "Nantes",
        "arrivee": "Rennes",
        "date": "2025-10-22",
        "heureDepart": "09:00",
        "heureArrivee": "10:30",
        "prix": 8,
        "placesDisponibles": 2,
        "ecologique": false,
        "vehiculeId": 204,
        "avis": []
      },
      {
        "id": 5,
        "depart": "Rennes",
        "arrivee": "Paris",
        "date": "2025-10-25",
        "heureDepart": "16:00",
        "heureArrivee": "19:30",
        "prix": 25,
        "placesDisponibles": 3,
        "ecologique": false,
        "vehiculeId": 204,
        "avis": [
          {
            "id": 1005,
            "passager": "ThomasR",
            "note": 4,
            "commentaire": "Bon conducteur, un peu rapide mais sûr.",
            "date": "2025-10-15"
          }
        ]
      }
    ]
  },
  {
    "conducteur": {
      "id": 104,
      "pseudo": "SophieM",
      "nom": "Sophie Martin",
      "photo": "images/avatars/sophie_martin.jpg",
      "note": 4.9,
      "email": "sophie.martin@example.com",
      "telephone": "0645678901",
      "vehicules": [
        {
          "id": 205,
          "modele": "Toyota Prius",
          "marque": "Toyota",
          "couleur": "Vert",
          "energie": "Hybride",
          "immatriculation": "IJ-456-KL",
          "places": 4
        }
      ],
      "preferences": {
        "fumeur": false,
        "animaux": true,
        "musique": "Sans préférence",
        "autres": "Préfère les trajets calmes"
      }
    },
    "trajets": [
      {
        "id": 6,
        "depart": "Lille",
        "arrivee": "Amiens",
        "date": "2025-10-19",
        "heureDepart": "13:00",
        "heureArrivee": "14:45",
        "prix": 10,
        "placesDisponibles": 1,
        "ecologique": true,
        "vehiculeId": 205,
        "avis": [
          {
            "id": 1006,
            "passager": "EmmaD",
            "note": 5,
            "commentaire": "Excellente conductrice, très attentionnée.",
            "date": "2025-10-10"
          }
        ]
      }
    ]
  }
];

const villesFrancaises = [
  { "nom": "Paris", "code": "75", "region": "Île-de-France", "lat": 48.8566, "lon": 2.3522 },
  { "nom": "Marseille", "code": "13", "region": "Provence-Alpes-Côte d'Azur", "lat": 43.2965, "lon": 5.3698 },
  { "nom": "Lyon", "code": "69", "region": "Auvergne-Rhône-Alpes", "lat": 45.7640, "lon": 4.8357 },
  { "nom": "Toulouse", "code": "31", "region": "Occitanie", "lat": 43.6047, "lon": 1.4442 },
  { "nom": "Nice", "code": "06", "region": "Provence-Alpes-Côte d'Azur", "lat": 43.7102, "lon": 7.2620 },
  { "nom": "Nantes", "code": "44", "region": "Pays de la Loire", "lat": 47.2184, "lon": -1.5536 },
  { "nom": "Montpellier", "code": "34", "region": "Occitanie", "lat": 43.6109, "lon": 3.8772 },
  { "nom": "Strasbourg", "code": "67", "region": "Grand Est", "lat": 48.5734, "lon": 7.7521 },
  { "nom": "Bordeaux", "code": "33", "region": "Nouvelle-Aquitaine", "lat": 44.8378, "lon": -0.5792 },
  { "nom": "Lille", "code": "59", "region": "Hauts-de-France", "lat": 50.6292, "lon": 3.0573 },
  { "nom": "Rennes", "code": "35", "region": "Bretagne", "lat": 48.1173, "lon": -1.6778 },
  { "nom": "Reims", "code": "51", "region": "Grand Est", "lat": 49.2583, "lon": 4.0317 },
  { "nom": "Le Havre", "code": "76", "region": "Normandie", "lat": 49.4944, "lon": 0.1079 },
  { "nom": "Saint-Étienne", "code": "42", "region": "Auvergne-Rhône-Alpes", "lat": 45.4397, "lon": 4.3872 },
  { "nom": "Toulon", "code": "83", "region": "Provence-Alpes-Côte d'Azur", "lat": 43.1242, "lon": 5.9280 },
  { "nom": "Grenoble", "code": "38", "region": "Auvergne-Rhône-Alpes", "lat": 45.1885, "lon": 5.7245 },
  { "nom": "Dijon", "code": "21", "region": "Bourgogne-Franche-Comté", "lat": 47.3220, "lon": 5.0415 },
  { "nom": "Angers", "code": "49", "region": "Pays de la Loire", "lat": 47.4784, "lon": -0.5632 },
  { "nom": "Nîmes", "code": "30", "region": "Occitanie", "lat": 43.8367, "lon": 4.3601 },
  { "nom": "Villeurbanne", "code": "69", "region": "Auvergne-Rhône-Alpes", "lat": 45.7640, "lon": 4.8847 },
  { "nom": "Clermont-Ferrand", "code": "63", "region": "Auvergne-Rhône-Alpes", "lat": 45.7772, "lon": 3.0870 },
  { "nom": "Aix-en-Provence", "code": "13", "region": "Provence-Alpes-Côte d'Azur", "lat": 43.5297, "lon": 5.4474 },
  { "nom": "Brest", "code": "29", "region": "Bretagne", "lat": 48.3904, "lon": -4.4861 },
  { "nom": "Limoges", "code": "87", "region": "Nouvelle-Aquitaine", "lat": 45.8336, "lon": 1.2611 },
  { "nom": "Tours", "code": "37", "region": "Centre-Val de Loire", "lat": 47.3941, "lon": 0.6848 },
  { "nom": "Amiens", "code": "80", "region": "Hauts-de-France", "lat": 49.8941, "lon": 2.2957 },
  { "nom": "Perpignan", "code": "66", "region": "Occitanie", "lat": 42.6976, "lon": 2.8954 },
  { "nom": "Metz", "code": "57", "region": "Grand Est", "lat": 49.1193, "lon": 6.1757 },
  { "nom": "Besançon", "code": "25", "region": "Bourgogne-Franche-Comté", "lat": 47.2378, "lon": 6.0241 },
  { "nom": "Orléans", "code": "45", "region": "Centre-Val de Loire", "lat": 47.9029, "lon": 1.9093 },
  { "nom": "Mulhouse", "code": "68", "region": "Grand Est", "lat": 47.7508, "lon": 7.3359 },
  { "nom": "Caen", "code": "14", "region": "Normandie", "lat": 49.1828, "lon": -0.3719 },
  { "nom": "Rouen", "code": "76", "region": "Normandie", "lat": 49.4432, "lon": 1.0999 }
];
/*


/*
function afficherTrajets() {
  const container = document.getElementById('trajets-container');

  // Parcourir chaque conducteur et ses trajets
  conducteursEtTrajets.forEach((conducteurData) => {
    const conducteur = conducteurData.conducteur;
    const trajets = conducteurData.trajets;

    // Créer une carte pour chaque trajet
    trajets.forEach((trajet) => {
      // Trouver le véhicule utilisé pour ce trajet
      const vehicule = conducteur.vehicules.find(v => v.id === trajet.vehiculeId);

      // Créer la carte du trajet
      const trajetCard = document.createElement('div');
      trajetCard.className = 'card mb-3 trajet-card';
      trajetCard.innerHTML = `
        <div class="row g-0">
          <div class="col-md-2 d-flex align-items-center justify-content-center p-3">
            <img src="${conducteur.photo}" class="img-fluid rounded-circle" alt="${conducteur.pseudo}" style="max-width: 80px;">
          </div>
          <div class="col-md-7">
            <div class="card-body">
              <h5 class="card-title">${trajet.depart} → ${trajet.arrivee}</h5>
              <p class="card-text">
                <strong>Conducteur:</strong> ${conducteur.pseudo} (${conducteur.note}/5) <br>
                <strong>Véhicule:</strong> ${vehicule.marque} ${vehicule.modele} (${vehicule.energie}) <br>
                <strong>Départ:</strong> ${trajet.date} à ${trajet.heureDepart} |
                <strong>Arrivée:</strong> ${trajet.heureArrivee} <br>
                <strong>Places disponibles:</strong> ${trajet.placesDisponibles} |
                <strong>Prix:</strong> ${trajet.prix} crédits |
                <strong>Écologique:</strong> ${trajet.ecologique ? 'Oui' : 'Non'}
              </p>
              <div class="preferences">
                <small><strong>Préférences:</strong> ${conducteur.preferences.fumeur ? 'Fumeur accepté' : 'Non-fumeur'}, ${conducteur.preferences.animaux ? 'Animaux acceptés' : 'Pas d\'animaux'}</small>
              </div>
            </div>
          </div>
          <div class="col-md-3 d-flex align-items-center justify-content-center p-3">
            <div class="text-center">
              <a href="detail.html?id=${trajet.id}" class="btn btn-primary">Détails</a>
              ${trajet.placesDisponibles > 0 ? `<button class="btn btn-success mt-2" onclick="participerAuTrajet(${trajet.id})">Participer</button>` : '<button class="btn btn-secondary mt-2" disabled>Complet</button>'}
            </div>
          </div>
        </div>
      `;
      container.appendChild(trajetCard);
    });
  });
}

// Exemple de fonction pour gérer la participation (à adapter selon ton back-end)
function participerAuTrajet(trajetId) {
  alert(`Vous allez participer au trajet ID: ${trajetId}. Cette action sera confirmée.`);
  // Ici, tu peux ajouter une logique pour envoyer une requête à ton API back-end
}
*/


/*

// Fonction pour afficher les trajets
    function afficherTrajetsAdaptes() {
      const container = document.getElementById('trajets-container');

      conducteursEtTrajets.forEach((conducteurData) => {
        const conducteur = conducteurData.conducteur;
        const trajets = conducteurData.trajets;

        trajets.forEach((trajet) => {
          const vehicule = conducteur.vehicules.find(v => v.id === trajet.vehiculeId);

          const trajetCol = document.createElement('div');
          trajetCol.className = 'col-md-4 mb-4';
          trajetCol.innerHTML = `
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <img src="${conducteur.photo}" alt="${conducteur.pseudo}" class="rounded-circle me-3" width="50" height="50">
                  <div>
                    <h6 class="mb-0">🚗 ${conducteur.pseudo}</h6>
                    <small class="text-warning">⭐ ${conducteur.note}</small>
                  </div>
                </div>
                <p class="mb-1"><strong>${trajet.depart}</strong> → <strong>${trajet.arrivee}</strong></p>
                <p class="mb-1 text-muted">Départ : ${trajet.date} - ${trajet.heureDepart}<br>Arrivée estimée : ${trajet.date} - ${trajet.heureArrivee}</p>
                <p class="mb-1">Places dispo : <span class="fw-bold">${trajet.placesDisponibles}</span></p>
                <p class="mb-1">Prix : <span class="fw-bold text-success">${trajet.prix} crédits</span></p>
                <p class="mb-1">${trajet.ecologique ? '🌱 <span class="text-success">Voyage écologique</span>' : '🚗 <span class="text-muted">Voyage standard</span>'}</p>
              </div>
              <div class="card-footer bg-white border-0">
                <a href="detail.html?id=${trajet.id}" class="btn btn-success w-100">Voir le détail</a>
              </div>
            </div>
          `;
          container.appendChild(trajetCol);
        });
      });
    }

    // Appeler la fonction quand la page est chargée
    document.addEventListener('DOMContentLoaded', afficherTrajetsAdaptes); 
*/



/*
// Fonction pour remplir les sélecteurs de villes
function remplirSelecteursVilles() {
    const departSelect = document.getElementById('depart');
    const arriveeSelect = document.getElementById('arrivee');

    villesFrancaises.forEach(ville => {
    const optionDepart = document.createElement('option');
    optionDepart.value = ville.nom;
    optionDepart.textContent = ville.nom;
    departSelect.appendChild(optionDepart);

    const optionArrivee = document.createElement('option');
    optionArrivee.value = ville.nom;
    optionArrivee.textContent = ville.nom;
    arriveeSelect.appendChild(optionArrivee);
    });
}

// Fonction pour afficher les trajets filtrés
function afficherTrajetsFiltres(depart, arrivee) {
    const container = document.getElementById('trajets-container');
    container.innerHTML = ''; // Vider le conteneur

    // Filtrer les trajets en fonction des villes sélectionnées
    const trajetsFiltres = [];
    conducteursEtTrajets.forEach(conducteurData => {
    const trajets = conducteurData.trajets.filter(trajet =>
        trajet.depart === depart && trajet.arrivee === arrivee
    );
    if (trajets.length > 0) {
        trajetsFiltres.push({ conducteur: conducteurData.conducteur, trajets });
    }
    });

    // Afficher un message si aucun trajet n'est trouvé
    if (trajetsFiltres.length === 0) {
    container.innerHTML = `
        <div class="col-12 text-center">
        <div class="alert alert-info">Aucun trajet trouvé pour ${depart} → ${arrivee}.</div>
        </div>
    `;
    return;
    }

    // Afficher les trajets filtrés
    trajetsFiltres.forEach(({ conducteur, trajets }) => {
    trajets.forEach(trajet => {
        const trajetCol = document.createElement('div');
        trajetCol.className = 'row-md-4 mb-4';
        trajetCol.innerHTML = `
        <div class="card shadow-sm h-100">
            <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <img src="${conducteur.photo}" alt="${conducteur.pseudo}" class="rounded-circle me-3" width="50" height="50">
                <div>
                <h6 class="mb-0">🚗 ${conducteur.pseudo}</h6>
                <small class="text-warning">⭐ ${conducteur.note}</small>
                </div>
            </div>
            <p class="mb-1"><strong>${trajet.depart}</strong> → <strong>${trajet.arrivee}</strong></p>
            <p class="mb-1 text-muted">Départ : ${trajet.date} - ${trajet.heureDepart}<br>Arrivée estimée : ${trajet.date} - ${trajet.heureArrivee}</p>
            <p class="mb-1">Places dispo : <span class="fw-bold">${trajet.placesDisponibles}</span></p>
            <p class="mb-1">Prix : <span class="fw-bold text-success">${trajet.prix} crédits</span></p>
            <p class="mb-1">${trajet.ecologique ? '🌱 <span class="text-success">Voyage écologique</span>' : '🚗 <span class="text-muted">Voyage standard</span>'}</p>
            </div>
            <div class="card-footer bg-white border-0">
            <a href="detail.html?id=${trajet.id}" class="btn btn-success w-100">Voir le détail</a>
            </div>
        </div>
        `;
        container.appendChild(trajetCol);
    });
    });
}

// Écouteur d'événement pour le bouton de recherche
document.getElementById('rechercher').addEventListener('click', () => {
    const depart = document.getElementById('depart').value;
    const arrivee = document.getElementById('arrivee').value;

    if (!depart || !arrivee) {
    alert('Veuillez sélectionner une ville de départ et une ville d\'arrivée.');
    return;
    }

    afficherTrajetsFiltres(depart, arrivee);
});

// Remplir les sélecteurs de villes au chargement de la page
document.addEventListener('DOMContentLoaded', remplirSelecteursVilles);
*/



/*
function afficherTrajetsAdaptes() {
  const container = document.getElementById('trajets-container'); // Assure-toi que ton conteneur a cet ID

  // Parcourir chaque conducteur et ses trajets
  conducteursEtTrajets.forEach((conducteurData) => {
    const conducteur = conducteurData.conducteur;
    const trajets = conducteurData.trajets;

    // Créer une carte pour chaque trajet
    trajets.forEach((trajet) => {
      // Trouver le véhicule utilisé pour ce trajet
      const vehicule = conducteur.vehicules.find(v => v.id === trajet.vehiculeId);

      // Créer la carte du trajet
      const trajetCol = document.createElement('div');
      trajetCol.className = 'col-md-4 mb-4 ';
      trajetCol.innerHTML = `
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <img src="${conducteur.photo}" alt="${conducteur.pseudo}" class="rounded-circle me-3" width="50" height="50">
              <div>
                <h6 class="mb-0">🚗 ${conducteur.pseudo}</h6>
                <small class="text-warning">⭐ ${conducteur.note}</small>
              </div>
            </div>
            <p class="mb-1"><strong>${trajet.depart}</strong> → <strong>${trajet.arrivee}</strong></p>
            <p class="mb-1 text-muted">Départ : ${trajet.date} - ${trajet.heureDepart}<br>Arrivée estimée : ${trajet.date} - ${trajet.heureArrivee}</p>
            <p class="mb-1">Places dispo : <span class="fw-bold">${trajet.placesDisponibles}</span></p>
            <p class="mb-1">Prix : <span class="fw-bold text-success">${trajet.prix} crédits</span></p>
            <p class="mb-1">${trajet.ecologique ? '🌱 <span class="text-success">Voyage écologique</span>' : '🚗 <span class="text-muted">Voyage standard</span>'}</p>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="detail.html?id=${trajet.id}" class="btn btn-success w-100">Voir le détail</a>
          </div>
        </div>
      `;
      container.appendChild(trajetCol);
    });
  });
}

// Appeler la fonction quand la page est chargée
document.addEventListener('DOMContentLoaded', afficherTrajetsAdaptes);
*/




// trajets.js pour covoiturages.html

// Tableau des conducteurs et trajets
const conducteursEtTrajets = [
  {
    "conducteur": {
      "id": 101,
      "pseudo": "Alex_75",
      "nom": "Alexandre Martin",
      "photo": "img/alex.jpg",
      "note": 4.8,
      "email": "alex.martin@example.com",
      "telephone": "0612345678"
    },
    "trajets": [
      {
        "id": 1,
        "depart": "Paris",
        "arrivee": "Lyon",
        "date": "2025-10-15",
        "heureDepart": "08:00",
        "heureArrivee": "12:30",
        "prix": 15,
        "placesDisponibles": 2,
        "ecologique": true,
        "vehicule": {
          "modele": "Tesla Model 3",
          "marque": "Tesla",
          "couleur": "Blanc",
          "energie": "Électrique"
        }
      }
    ]
  },
  {
    "conducteur": {
      "id": 102,
      "pseudo": "MarieL",
      "nom": "Marie Lambert",
      "photo": "img/marie.jpg",
      "note": 4.5,
      "email": "marie.lambert@example.com",
      "telephone": "0623456789"
    },
    "trajets": [
      {
        "id": 2,
        "depart": "Lyon",
        "arrivee": "Marseille",
        "date": "2025-10-20",
        "heureDepart": "14:00",
        "heureArrivee": "17:30",
        "prix": 20,
        "placesDisponibles": 1,
        "ecologique": true,
        "vehicule": {
          "modele": "Renault Zoé",
          "marque": "Renault",
          "couleur": "Bleu",
          "energie": "Électrique"
        }
      },
      {
        "id": 3,
        "depart": "Marseille",
        "arrivee": "Nice",
        "date": "2025-10-22",
        "heureDepart": "09:00",
        "heureArrivee": "11:30",
        "prix": 12,
        "placesDisponibles": 3,
        "ecologique": false,
        "vehicule": {
          "modele": "Peugeot 308",
          "marque": "Peugeot",
          "couleur": "Gris",
          "energie": "Essence"
        }
      }
    ]
  },
  {
    "conducteur": {
      "id": 103,
      "pseudo": "PierreT",
      "nom": "Pierre Tremblay",
      "photo": "img/pierre.jpg",
      "note": 4.2,
      "email": "pierre.tremblay@example.com",
      "telephone": "0634567890"
    },
    "trajets": [
      {
        "id": 4,
        "depart": "Nantes",
        "arrivee": "Rennes",
        "date": "2025-10-25",
        "heureDepart": "09:00",
        "heureArrivee": "10:30",
        "prix": 8,
        "placesDisponibles": 2,
        "ecologique": false,
        "vehicule": {
          "modele": "Citroën C4",
          "marque": "Citroën",
          "couleur": "Gris",
          "energie": "Essence"
        }
      },
      {
        "id": 5,
        "depart": "Rennes",
        "arrivee": "Paris",
        "date": "2025-10-28",
        "heureDepart": "16:00",
        "heureArrivee": "19:30",
        "prix": 25,
        "placesDisponibles": 3,
        "ecologique": false,
        "vehicule": {
          "modele": "Volkswagen Golf",
          "marque": "Volkswagen",
          "couleur": "Noir",
          "energie": "Diesel"
        }
      }
    ]
  },
  {
    "conducteur": {
      "id": 104,
      "pseudo": "SophieM",
      "nom": "Sophie Martin",
      "photo": "img/sophie.jpg",
      "note": 4.9,
      "email": "sophie.martin@example.com",
      "telephone": "0645678901"
    },
    "trajets": [
      {
        "id": 6,
        "depart": "Lille",
        "arrivee": "Amiens",
        "date": "2025-10-19",
        "heureDepart": "13:00",
        "heureArrivee": "14:45",
        "prix": 10,
        "placesDisponibles": 1,
        "ecologique": true,
        "vehicule": {
          "modele": "Toyota Prius",
          "marque": "Toyota",
          "couleur": "Vert",
          "energie": "Hybride"
        }
      }
    ]
  }
];

// Fonction pour afficher les trajets filtrés
function afficherTrajetsFiltres(depart, arrivee, date) {
  const container = document.getElementById('trajets-container');
  container.innerHTML = ''; // Vider le conteneur

  // Filtrer les trajets en fonction des critères de recherche
  const trajetsFiltres = [];
  conducteursEtTrajets.forEach(conducteurData => {
    const trajets = conducteurData.trajets.filter(trajet =>
      trajet.depart === depart &&
      trajet.arrivee === arrivee &&
      trajet.date === date
    );
    if (trajets.length > 0) {
      trajetsFiltres.push({ conducteur: conducteurData.conducteur, trajets });
    }
  });

  // Afficher un message si aucun trajet n'est trouvé
  if (trajetsFiltres.length === 0) {
    container.innerHTML = `
      <div class="col-12 text-center">
        <div class="alert alert-info">Aucun trajet trouvé pour ${depart} → ${arrivee} le ${date}.</div>
      </div>
    `;
    return;
  }

  // Afficher les trajets filtrés
  trajetsFiltres.forEach(({ conducteur, trajets }) => {
    trajets.forEach(trajet => {
      const trajetCol = document.createElement('div');
      trajetCol.className = 'col-md-4 mb-4';
      trajetCol.innerHTML = `
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <img src="${conducteur.photo}" alt="${conducteur.pseudo}" class="rounded-circle me-3" width="50" height="50">
              <div>
                <h6 class="mb-0">🚗 ${conducteur.pseudo}</h6>
                <small class="text-warning">⭐ ${conducteur.note}</small>
              </div>
            </div>
            <p class="mb-1"><strong>${trajet.depart}</strong> → <strong>${trajet.arrivee}</strong></p>
            <p class="mb-1 text-muted">Départ : ${trajet.date} - ${trajet.heureDepart}<br>Arrivée estimée : ${trajet.date} - ${trajet.heureArrivee}</p>
            <p class="mb-1">Places dispo : <span class="fw-bold">${trajet.placesDisponibles}</span></p>
            <p class="mb-1">Prix : <span class="fw-bold text-success">${trajet.prix} crédits</span></p>
            <p class="mb-1">${trajet.ecologique ? '🌱 <span class="text-success">Voyage écologique</span>' : '🚗 <span class="text-muted">Voyage standard</span>'}</p>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="detail.html?id=${trajet.id}" class="btn btn-success w-100">Voir le détail</a>
          </div>
        </div>
      `;
      container.appendChild(trajetCol);
    });
  });
}

// Récupérer les paramètres de recherche depuis l'URL
function getUrlParams() {
  const params = new URLSearchParams(window.location.search);
  return {
    depart: params.get('depart'),
    arrivee: params.get('arrivee'),
    date: params.get('date')
  };
}

// Afficher les trajets filtrés au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
  const { depart, arrivee, date } = getUrlParams();

  if (depart && arrivee && date) {
    document.getElementById('titre-recherche').textContent = `Trajets de ${depart} à ${arrivee} le ${date}`;
    afficherTrajetsFiltres(depart, arrivee, date);
  } else {
    document.getElementById('titre-recherche').textContent = 'Tous les trajets';
    // Afficher tous les trajets si aucun paramètre n'est spécifié
    afficherTousLesTrajets();
  }
});


/*
// Fonction pour afficher tous les trajets (optionnel)
function afficherTousLesTrajets() {
  const container = document.getElementById('trajets-container');
  container.innerHTML = '';

  conducteursEtTrajets.forEach(conducteurData => {
    const conducteur = conducteurData.conducteur;
    const trajets = conducteurData.trajets;

    trajets.forEach(trajet => {
      const trajetCol = document.createElement('div');
      trajetCol.className = 'col-md-4 mb-4';
      trajetCol.innerHTML = `
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <img src="${conducteur.photo}" alt="${conducteur.pseudo}" class="rounded-circle me-3" width="50" height="50">
              <div>
                <h6 class="mb-0">🚗 ${conducteur.pseudo}</h6>
                <small class="text-warning">⭐ ${conducteur.note}</small>
              </div>
            </div>
            <p class="mb-1"><strong>${trajet.depart}</strong> → <strong>${trajet.arrivee}</strong></p>
            <p class="mb-1 text-muted">Départ : ${trajet.date} - ${trajet.heureDepart}<br>Arrivée estimée : ${trajet.date} - ${trajet.heureArrivee}</p>
            <p class="mb-1">Places dispo : <span class="fw-bold">${trajet.placesDisponibles}</span></p>
            <p class="mb-1">Prix : <span class="fw-bold text-success">${trajet.prix} crédits</span></p>
            <p class="mb-1">${trajet.ecologique ? '🌱 <span class="text-success">Voyage écologique</span>' : '🚗 <span class="text-muted">Voyage standard</span>'}</p>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="detail.html?id=${trajet.id}" class="btn btn-success w-100">Voir le détail</a>
          </div>
        </div>
      `;
      container.appendChild(trajetCol);
    });
  });
}*/

/*
// Fonction pour afficher tous les trajets avec des attributs de filtre
function afficherTousLesTrajets() {
  const container = document.getElementById('trajets-container');
  container.innerHTML = ''; // Vider le conteneur

  // Parcourir chaque conducteur et ses trajets
  conducteursEtTrajets.forEach((conducteurData) => {
    const conducteur = conducteurData.conducteur;
    const trajets = conducteurData.trajets;

    trajets.forEach((trajet) => {
      const trajetCol = document.createElement('div');
      trajetCol.className = 'col-md-4 mb-4';
      // Ajouter des attributs pour les filtres
      trajetCol.setAttribute('data-ecologique', trajet.ecologique);
      trajetCol.setAttribute('data-prix', trajet.prix);
      trajetCol.setAttribute('data-note', conducteur.note);
      trajetCol.setAttribute('data-places', trajet.placesDisponibles);

      trajetCol.innerHTML = `
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <img src="${conducteur.photo}" alt="${conducteur.pseudo}" class="rounded-circle me-3" width="50" height="50">
              <div>
                <h6 class="mb-0">🚗 ${conducteur.pseudo}</h6>
                <small class="text-warning">⭐ ${conducteur.note}</small>
              </div>
            </div>
            <p class="mb-1"><strong>${trajet.depart}</strong> → <strong>${trajet.arrivee}</strong></p>
            <p class="mb-1 text-muted">Départ : ${trajet.date} - ${trajet.heureDepart}<br>Arrivée estimée : ${trajet.date} - ${trajet.heureArrivee}</p>
            <p class="mb-1">Places dispo : <span class="fw-bold">${trajet.placesDisponibles}</span></p>
            <p class="mb-1">Prix : <span class="fw-bold text-success">${trajet.prix} crédits</span></p>
            <p class="mb-1">${trajet.ecologique ? '🌱 <span class="text-success">Voyage écologique</span>' : '🚗 <span class="text-muted">Voyage standard</span>'}</p>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="detail.html?id=${trajet.id}" class="btn btn-success w-100">Voir le détail</a>
          </div>
        </div>
      `;
      container.appendChild(trajetCol);
    });
  });
}

// Fonction pour appliquer tous les filtres
function appliquerFiltres() {
  const filtreEco = document.getElementById('filtreEco').checked;
  const filtrePrix = parseFloat(document.getElementById('filtrePrix').value) || Infinity;
  const filtreNote = parseFloat(document.getElementById('filtreNote').value) || 0;
  const filtrePlaces = parseInt(document.getElementById('filtrePlaces').value) || 0;

  const trajets = document.querySelectorAll('#trajets-container > div');

  trajets.forEach(trajet => {
    const estEcologique = trajet.getAttribute('data-ecologique') === 'true';
    const prix = parseFloat(trajet.getAttribute('data-prix'));
    const note = parseFloat(trajet.getAttribute('data-note'));
    const places = parseInt(trajet.getAttribute('data-places'));

    // Appliquer les filtres
    const passeFiltreEco = !filtreEco || estEcologique;
    const passeFiltrePrix = prix <= filtrePrix;
    const passeFiltreNote = note >= filtreNote;
    const passeFiltrePlaces = places >= filtrePlaces;

    // Afficher ou masquer en fonction des filtres
    trajet.style.display = (passeFiltreEco && passeFiltrePrix && passeFiltreNote && passeFiltrePlaces) ? 'block' : 'none';
  });
}

// Écouteurs d'événements pour les filtres
document.getElementById('filtreEco').addEventListener('change', appliquerFiltres);
document.getElementById('filtrePrix').addEventListener('input', appliquerFiltres);
document.getElementById('filtreNote').addEventListener('change', appliquerFiltres);
document.getElementById('filtrePlaces').addEventListener('change', appliquerFiltres);

// Afficher tous les trajets au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
  afficherTousLesTrajets();
});
*/









// Fonction pour afficher tous les trajets avec des attributs de filtre
function afficherTousLesTrajets() {
  const container = document.getElementById('trajets-container');
  container.innerHTML = ''; // Vider le conteneur

  // Parcourir chaque conducteur et ses trajets
  conducteursEtTrajets.forEach((conducteurData) => {
    const conducteur = conducteurData.conducteur;
    const trajets = conducteurData.trajets;

    trajets.forEach((trajet) => {
      const trajetCol = document.createElement('div');
      trajetCol.className = 'col-md-4 mb-4';
      // Ajouter des attributs pour les filtres
      trajetCol.setAttribute('data-ecologique', trajet.ecologique);
      trajetCol.setAttribute('data-prix', trajet.prix);
      trajetCol.setAttribute('data-note', conducteur.note);
      trajetCol.setAttribute('data-places', trajet.placesDisponibles);
      trajetCol.setAttribute('data-date', trajet.date);

      trajetCol.innerHTML = `
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center mb-3">
              <img src="${conducteur.photo}" alt="${conducteur.pseudo}" class="rounded-circle me-3" width="50" height="50">
              <div>
                <h6 class="mb-0">🚗 ${conducteur.pseudo}</h6>
                <small class="text-warning">⭐ ${conducteur.note}</small>
              </div>
            </div>
            <p class="mb-1"><strong>${trajet.depart}</strong> → <strong>${trajet.arrivee}</strong></p>
            <p class="mb-1 text-muted">Départ : ${trajet.date} - ${trajet.heureDepart}<br>Arrivée estimée : ${trajet.date} - ${trajet.heureArrivee}</p>
            <p class="mb-1">Places dispo : <span class="fw-bold">${trajet.placesDisponibles}</span></p>
            <p class="mb-1">Prix : <span class="fw-bold text-success">${trajet.prix} crédits</span></p>
            <p class="mb-1">${trajet.ecologique ? '🌱 <span class="text-success">Voyage écologique</span>' : '🚗 <span class="text-muted">Voyage standard</span>'}</p>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="detail.html?id=${trajet.id}" class="btn btn-success w-100">Voir le détail</a>
          </div>
        </div>
      `;
      container.appendChild(trajetCol);
    });
  });
}

// Fonction pour trouver les dates alternatives si aucun trajet n'est disponible
function trouverDatesAlternatives(depart, arrivee, dateRecherche) {
  const datesDisponibles = new Set();
  const trajetsDisponibles = [];

  // Parcourir tous les trajets pour trouver des dates alternatives
  conducteursEtTrajets.forEach((conducteurData) => {
    conducteurData.trajets.forEach((trajet) => {
      if (trajet.depart === depart && trajet.arrivee === arrivee && trajet.placesDisponibles > 0) {
        datesDisponibles.add(trajet.date);
        trajetsDisponibles.push(trajet);
      }
    });
  });

  // Convertir les dates en objets Date pour les trier
  const datesTriees = Array.from(datesDisponibles)
    .map(dateStr => new Date(dateStr.split('/').reverse().join('-')))
    .sort((a, b) => a - b)
    .map(date => {
      const jour = String(date.getDate()).padStart(2, '0');
      const mois = String(date.getMonth() + 1).padStart(2, '0');
      const annee = date.getFullYear();
      return `${jour}/${mois}/${annee}`;
    });

  // Trouver la première date disponible après la date recherchée
  const dateRechercheObj = new Date(dateRecherche.split('/').reverse().join('-'));
  const premiereDateAlternative = datesTriees.find(dateStr => {
    const dateObj = new Date(dateStr.split('/').reverse().join('-'));
    return dateObj > dateRechercheObj;
  });

  // Retourner la première date alternative et les trajets correspondants
  if (premiereDateAlternative) {
    const trajetsPourDateAlternative = trajetsDisponibles.filter(trajet => trajet.date === premiereDateAlternative);
    return {
      date: premiereDateAlternative,
      trajets: trajetsPourDateAlternative
    };
  }

  return null;
}

// Fonction pour appliquer tous les filtres
function appliquerFiltres() {
  const filtreEco = document.getElementById('filtreEco').checked;
  const filtrePrix = parseFloat(document.getElementById('filtrePrix').value) || Infinity;
  const filtreNote = parseFloat(document.getElementById('filtreNote').value) || 0;
  const filtrePlaces = parseInt(document.getElementById('filtrePlaces').value) || 0;
  const filtreDate = document.getElementById('filtreDate').value;

  const trajets = document.querySelectorAll('#trajets-container > div');
  let trajetsVisibles = 0;

  trajets.forEach(trajet => {
    const estEcologique = trajet.getAttribute('data-ecologique') === 'true';
    const prix = parseFloat(trajet.getAttribute('data-prix'));
    const note = parseFloat(trajet.getAttribute('data-note'));
    const places = parseInt(trajet.getAttribute('data-places'));
    const dateTrajet = trajet.getAttribute('data-date');

    // Appliquer les filtres
    const passeFiltreEco = !filtreEco || estEcologique;
    const passeFiltrePrix = prix <= filtrePrix;
    const passeFiltreNote = note >= filtreNote;
    const passeFiltrePlaces = places >= filtrePlaces;
    const passeFiltreDate = !filtreDate || dateTrajet === filtreDate;

    // Afficher ou masquer en fonction des filtres
    if (passeFiltreEco && passeFiltrePrix && passeFiltreNote && passeFiltrePlaces && passeFiltreDate) {
      trajet.style.display = 'block';
      trajetsVisibles++;
    } else {
      trajet.style.display = 'none';
    }
  });

  // Si aucun trajet n'est visible, proposer des dates alternatives
  if (trajetsVisibles === 0 && filtreDate) {
    const [depart, arrivee] = document.getElementById('titre-recherche').textContent
      .replace('Trajets de ', '')
      .replace(' à ', '→')
      .split('→')
      .map(ville => ville.trim());

    const dateAlternative = trouverDatesAlternatives(depart, arrivee, filtreDate);

    if (dateAlternative) {
      const container = document.getElementById('trajets-container');
      const suggestion = document.createElement('div');
      suggestion.className = 'col-12 mb-4';
      suggestion.innerHTML = `
        <div class="alert alert-info">
          <h5>Aucun trajet disponible pour le ${filtreDate}.</h5>
          <p>Voici des trajets disponibles pour le <strong>${dateAlternative.date}</strong> :</p>
        </div>
      `;
      container.prepend(suggestion);

      // Afficher les trajets pour la date alternative
      dateAlternative.trajets.forEach(trajet => {
        const conducteur = conducteursEtTrajets
          .find(c => c.trajets.some(t => t.id === trajet.id))
          .conducteur;

        const trajetCol = document.createElement('div');
        trajetCol.className = 'col-md-4 mb-4';
        trajetCol.innerHTML = `
          <div class="card shadow-sm h-100 border-primary">
            <div class="card-body">
              <div class="d-flex align-items-center mb-3">
                <img src="${conducteur.photo}" alt="${conducteur.pseudo}" class="rounded-circle me-3" width="50" height="50">
                <div>
                  <h6 class="mb-0">🚗 ${conducteur.pseudo}</h6>
                  <small class="text-warning">⭐ ${conducteur.note}</small>
                </div>
              </div>
              <p class="mb-1"><strong>${trajet.depart}</strong> → <strong>${trajet.arrivee}</strong></p>
              <p class="mb-1 text-muted">Départ : ${trajet.date} - ${trajet.heureDepart}<br>Arrivée estimée : ${trajet.date} - ${trajet.heureArrivee}</p>
              <p class="mb-1">Places dispo : <span class="fw-bold">${trajet.placesDisponibles}</span></p>
              <p class="mb-1">Prix : <span class="fw-bold text-success">${trajet.prix} crédits</span></p>
              <p class="mb-1">${trajet.ecologique ? '🌱 <span class="text-success">Voyage écologique</span>' : '🚗 <span class="text-muted">Voyage standard</span>'}</p>
            </div>
            <div class="card-footer bg-white border-0">
              <a href="detail.html?id=${trajet.id}" class="btn btn-success w-100">Voir le détail</a>
            </div>
          </div>
        `;
        container.appendChild(trajetCol);
      });
    } else {
      const container = document.getElementById('trajets-container');
      const message = document.createElement('div');
      message.className = 'col-12';
      message.innerHTML = `
        <div class="alert alert-warning">
          Aucun trajet disponible pour le ${filtreDate} ou les dates suivantes.
        </div>
      `;
      container.prepend(message);
    }
  }
}

// Écouteurs d'événements pour les filtres
document.getElementById('filtreEco').addEventListener('change', appliquerFiltres);
document.getElementById('filtrePrix').addEventListener('input', appliquerFiltres);
document.getElementById('filtreNote').addEventListener('change', appliquerFiltres);
document.getElementById('filtrePlaces').addEventListener('change', appliquerFiltres);
document.getElementById('filtreDate').addEventListener('change', appliquerFiltres);

// Afficher tous les trajets au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
  afficherTousLesTrajets();

  // Récupérer les paramètres de recherche depuis l'URL
  const params = new URLSearchParams(window.location.search);
  const depart = params.get('depart');
  const arrivee = params.get('arrivee');
  const date = params.get('date');

  if (depart && arrivee && date) {
    document.getElementById('titre-recherche').textContent = `Trajets de ${depart} à ${arrivee}`;
    document.getElementById('filtreDate').value = date;
    appliquerFiltres();
  } else {
    document.getElementById('titre-recherche').textContent = 'Tous les trajets';
  }
});
