// Tableau des conducteurs et trajets
var conducteursEtTrajets = [
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
        "detail": "detail_alex.html",
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
        "detail": "detail_marie.html",
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
        "detail": "detail_marie.html",
        "vehicule": {
          "modele": "Peugeot 308",
          "marque": "Peugeot",
          "couleur": "Gris",
          "energie": "Essence"
        }
      },
      {
        "id": 14,
        "depart": "Paris",
        "arrivee": "Lyon",
        "date": "2025-10-15",
        "heureDepart": "09:30",
        "heureArrivee": "13:45",
        "prix": 18,
        "placesDisponibles": 2,
        "ecologique": true,
        "detail": "detail_marie.html",
        "vehicule": {
          "modele": "Renault Zoé",
          "marque": "Renault",
          "couleur": "Bleu",
          "energie": "Électrique"
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
        "detail": "detail_pierre.html",
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
        "detail": "detail_pierre.html",
        "vehicule": {
          "modele": "Volkswagen Golf",
          "marque": "Volkswagen",
          "couleur": "Noir",
          "energie": "Diesel"
        }
      },
      {
        "id": 15,
        "depart": "Paris",
        "arrivee": "Lyon",
        "date": "2025-10-15",
        "heureDepart": "11:15",
        "heureArrivee": "15:30",
        "prix": 20,
        "placesDisponibles": 1,
        "ecologique": false,
        "detail": "detail_pierre.html",
        "vehicule": {
          "modele": "Citroën C4",
          "marque": "Citroën",
          "couleur": "Gris",
          "energie": "Essence"
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
        "detail": "detail_sophie.html",
        "vehicule": {
          "modele": "Toyota Prius",
          "marque": "Toyota",
          "couleur": "Vert",
          "energie": "Hybride"
        }
      }
    ]
  },
  {
    "conducteur": {
      "id": 105,
      "pseudo": "C.Ronaldo7",
      "nom": "Cristiano Ronaldo",
      "photo": "img/ronaldo.jpg",
      "note": 7,
      "email": "cristiano.ronaldo@example.com",
      "telephone": "0645678981"
    },
    "trajets": [
      {
        "id": 7,
        "depart": "Paris",
        "arrivee": "Marseille",
        "date": "2025-10-30",
        "heureDepart": "12:00",
        "heureArrivee": "21:45",
        "prix": 7,
        "placesDisponibles": 1,
        "ecologique": false,
        "detail": "detail_ronaldo.html",
        "vehicule": {
          "modele": "Ferari 458",
          "marque": "Ferrari",
          "couleur": "Rouge",
          "energie": "Essence"
        }
      }
    ]
  },
  {
    "conducteur": {
      "id": 106,
      "pseudo": "JulieEco",
      "nom": "Julie Dupont",
      "photo": "img/julie.png",
      "note": 4.7,
      "email": "julie.dupont@example.com",
      "telephone": "0656789123"
    },
    "trajets": [
      {
        "id": 8,
        "depart": "Bordeaux",
        "arrivee": "Toulouse",
        "date": "2025-11-02",
        "heureDepart": "09:15",
        "heureArrivee": "12:00",
        "prix": 14,
        "placesDisponibles": 2,
        "ecologique": true,
        "detail": "detail_julie.html",
        "vehicule": {
          "modele": "Hyundai Kona",
          "marque": "Hyundai",
          "couleur": "Bleu",
          "energie": "Électrique"
        }
      },
      {
        "id": 9,
        "depart": "Toulouse",
        "arrivee": "Montpellier",
        "date": "2025-11-05",
        "heureDepart": "15:00",
        "heureArrivee": "18:10",
        "prix": 16,
        "placesDisponibles": 1,
        "ecologique": true,
        "detail": "detail_julie.html",
        "vehicule": {
          "modele": "Hyundai Kona",
          "marque": "Hyundai",
          "couleur": "Bleu",
          "energie": "Électrique"
        }
      },
      {
        "id": 16,
        "depart": "Paris",
        "arrivee": "Lyon",
        "date": "2025-10-15",
        "heureDepart": "07:45",
        "heureArrivee": "12:10",
        "prix": 17,
        "placesDisponibles": 3,
        "ecologique": true,
        "detail": "detail_julie.html",
        "vehicule": {
          "modele": "Hyundai Kona",
          "marque": "Hyundai",
          "couleur": "Bleu",
          "energie": "Électrique"
        }
      }
    ]
  },
  {
    "conducteur": {
      "id": 107,
      "pseudo": "LeoSud",
      "nom": "Léo Martinez",
      "photo": "img/leo.png",
      "note": 4.3,
      "email": "leo.martinez@example.com",
      "telephone": "0678912345"
    },
    "trajets": [
      {
        "id": 10,
        "depart": "Nice",
        "arrivee": "Aix-en-Provence",
        "date": "2025-11-08",
        "heureDepart": "07:30",
        "heureArrivee": "10:20",
        "prix": 18,
        "placesDisponibles": 3,
        "ecologique": false,
        "detail": "detail_leo.html",
        "vehicule": {
          "modele": "Peugeot 508",
          "marque": "Peugeot",
          "couleur": "Gris",
          "energie": "Diesel"
        }
      },
      {
        "id": 11,
        "depart": "Aix-en-Provence",
        "arrivee": "Lyon",
        "date": "2025-11-09",
        "heureDepart": "13:45",
        "heureArrivee": "18:30",
        "prix": 22,
        "placesDisponibles": 2,
        "ecologique": false,
        "detail": "detail_leo.html",
        "vehicule": {
          "modele": "Peugeot 508",
          "marque": "Peugeot",
          "couleur": "Gris",
          "energie": "Diesel"
        }
      }
    ]
  },
  {
    "conducteur": {
      "id": 108,
      "pseudo": "NinaZen",
      "nom": "Nina Leroy",
      "photo": "img/nina.png",
      "note": 4.9,
      "email": "nina.leroy@example.com",
      "telephone": "0689123456"
    },
    "trajets": [
      {
        "id": 12,
        "depart": "Strasbourg",
        "arrivee": "Metz",
        "date": "2025-11-12",
        "heureDepart": "08:10",
        "heureArrivee": "10:00",
        "prix": 11,
        "placesDisponibles": 4,
        "ecologique": true,
        "detail": "detail_nina.html",
        "vehicule": {
          "modele": "Volkswagen ID.4",
          "marque": "Volkswagen",
          "couleur": "Noir",
          "energie": "Électrique"
        }
      },
      {
        "id": 13,
        "depart": "Metz",
        "arrivee": "Reims",
        "date": "2025-11-13",
        "heureDepart": "14:30",
        "heureArrivee": "17:10",
        "prix": 13,
        "placesDisponibles": 2,
        "ecologique": true,
        "detail": "detail_nina.html",
        "vehicule": {
          "modele": "Volkswagen ID.4",
          "marque": "Volkswagen",
          "couleur": "Noir",
          "energie": "Électrique"
        }
      }
    ]
  }
];

var rechercheCourante = null;

function calculerDureeMinutes(heureDepart, heureArrivee) {
  if (!heureDepart || !heureArrivee) {
    return NaN;
  }

  var departSplit = heureDepart.split(':');
  var arriveeSplit = heureArrivee.split(':');

  if (departSplit.length !== 2 || arriveeSplit.length !== 2) {
    return NaN;
  }

  var departHeures = parseInt(departSplit[0], 10);
  var departMinutes = parseInt(departSplit[1], 10);
  var arriveeHeures = parseInt(arriveeSplit[0], 10);
  var arriveeMinutes = parseInt(arriveeSplit[1], 10);

  if (isNaN(departHeures) || isNaN(departMinutes) || isNaN(arriveeHeures) || isNaN(arriveeMinutes)) {
    return NaN;
  }

  var departTotal = departHeures * 60 + departMinutes;
  var arriveeTotal = arriveeHeures * 60 + arriveeMinutes;
  var difference = arriveeTotal - departTotal;

  if (difference < 0) {
    difference += 24 * 60;
  }

  return difference;
}

function creerCarteTrajet(conducteur, trajet, accent, isSuggestion) {
  if (typeof accent === 'undefined') {
    accent = false;
  }

  if (typeof isSuggestion === 'undefined') {
    isSuggestion = false;
  }

  var trajetCol = document.createElement('div');
  trajetCol.className = 'col-md-4 mb-4';
  trajetCol.dataset.trajetId = trajet.id;
  trajetCol.dataset.ecologique = trajet.ecologique ? 'true' : 'false';
  trajetCol.dataset.prix = '' + trajet.prix;
  trajetCol.dataset.note = '' + conducteur.note;
  trajetCol.dataset.places = '' + trajet.placesDisponibles;
  trajetCol.dataset.date = trajet.date;
  trajetCol.dataset.depart = trajet.depart;
  trajetCol.dataset.arrivee = trajet.arrivee;

  var dureeMinutes = calculerDureeMinutes(trajet.heureDepart, trajet.heureArrivee);
  if (!isNaN(dureeMinutes)) {
    trajetCol.dataset.duree = '' + dureeMinutes;
  }

  if (isSuggestion) {
    trajetCol.dataset.suggestion = 'true';
  }

  var cardClasses = 'card shadow-sm h-100';
  if (accent) {
    cardClasses += ' border-success';
  }

  var contenu = '';
  contenu += '<div class="' + cardClasses + '">';
  contenu += '<div class="card-body">';
  contenu += '<div class="d-flex align-items-center mb-3">';
  contenu += '<img src="' + conducteur.photo + '" alt="' + conducteur.pseudo + '" class="rounded-circle me-3" width="50" height="50">';
  contenu += '<div>';
  contenu += '<h6 class="mb-0">🚗 ' + conducteur.pseudo + '</h6>';
  contenu += '<small class="text-warning">⭐ ' + conducteur.note + '</small>';
  contenu += '</div>';
  contenu += '</div>';
  contenu += '<p class="mb-1"><strong>' + trajet.depart + '</strong> → <strong>' + trajet.arrivee + '</strong></p>';
  contenu += '<p class="mb-1 text-muted">Départ : ' + trajet.date + ' - ' + trajet.heureDepart + '<br>Arrivée estimée : ' + trajet.date + ' - ' + trajet.heureArrivee + '</p>';
  contenu += '<p class="mb-1">Places dispo : <span class="fw-bold">' + trajet.placesDisponibles + '</span></p>';
  contenu += '<p class="mb-1">Prix : <span class="fw-bold text-success">' + trajet.prix + ' crédits</span></p>';
  if (trajet.ecologique) {
    contenu += '<p class="mb-1">🌱 <span class="text-success">Voyage écologique</span></p>';
  } else {
    contenu += '<p class="mb-1">🚗 <span class="text-muted">Voyage standard</span></p>';
  }
  contenu += '</div>';
  contenu += '<div class="card-footer bg-white border-0">';
  contenu += '<a href="' + trajet.detail + '" class="btn btn-success w-100">Voir le détail</a>';
  contenu += '</div>';
  contenu += '</div>';

  trajetCol.innerHTML = contenu;

  return trajetCol;
}

function afficherTousLesTrajets(container) {
  container.innerHTML = '';

  for (var i = 0; i < conducteursEtTrajets.length; i++) {
    var conducteurData = conducteursEtTrajets[i];
    var conducteur = conducteurData.conducteur;

    for (var j = 0; j < conducteurData.trajets.length; j++) {
      var trajet = conducteurData.trajets[j];
      var carte = creerCarteTrajet(conducteur, trajet);
      container.appendChild(carte);
    }
  }
}

function formatDateFr(isoDate) {
  if (!isoDate) {
    return '';
  }

  var date = new Date(isoDate);
  if (isNaN(date.getTime())) {
    return isoDate;
  }

  return date.toLocaleDateString('fr-FR');
}

function trouverDatesAlternatives(depart, arrivee, dateRecherche) {
  var trajetsCompatibles = [];

  for (var i = 0; i < conducteursEtTrajets.length; i++) {
    var conducteurData = conducteursEtTrajets[i];
    for (var j = 0; j < conducteurData.trajets.length; j++) {
      var trajet = conducteurData.trajets[j];
      if (trajet.depart === depart && trajet.arrivee === arrivee && trajet.placesDisponibles > 0) {
        trajetsCompatibles.push(trajet);
      }
    }
  }

  if (trajetsCompatibles.length === 0) {
    return null;
  }

  var datesUniques = [];
  for (var k = 0; k < trajetsCompatibles.length; k++) {
    var dateCandidate = trajetsCompatibles[k].date;
    if (datesUniques.indexOf(dateCandidate) === -1) {
      datesUniques.push(dateCandidate);
    }
  }

  var rechercheTime = new Date(dateRecherche).getTime();
  if (isNaN(rechercheTime)) {
    return null;
  }

  var meilleureDate = null;
  var meilleureDifference = Infinity;

  for (var l = 0; l < datesUniques.length; l++) {
    var dateAlternative = datesUniques[l];
    var timeAlternative = new Date(dateAlternative).getTime();
    if (!isNaN(timeAlternative) && timeAlternative > rechercheTime) {
      var difference = timeAlternative - rechercheTime;
      if (difference < meilleureDifference) {
        meilleureDifference = difference;
        meilleureDate = dateAlternative;
      }
    }
  }

  if (!meilleureDate) {
    return null;
  }

  var trajetsPourDate = [];
  for (var m = 0; m < trajetsCompatibles.length; m++) {
    if (trajetsCompatibles[m].date === meilleureDate) {
      trajetsPourDate.push(trajetsCompatibles[m]);
    }
  }

  return {
    date: meilleureDate,
    trajets: trajetsPourDate
  };
}

function supprimerMessagesEtSuggestions(container) {
  var messages = container.querySelectorAll('[data-message="true"]');
  for (var i = 0; i < messages.length; i++) {
    container.removeChild(messages[i]);
  }

  var suggestions = container.querySelectorAll('[data-suggestion="true"]');
  for (var j = 0; j < suggestions.length; j++) {
    container.removeChild(suggestions[j]);
  }
}

function appliquerFiltres(container, filtres) {
  supprimerMessagesEtSuggestions(container);

  var trajetsCards = container.querySelectorAll('[data-trajet-id]');

  var filtreEco = filtres.eco ? filtres.eco.checked : false;
  var filtrePrix = filtres.prix && filtres.prix.value !== '' ? parseFloat(filtres.prix.value) : Infinity;
  var filtreNote = filtres.note && filtres.note.value !== '' ? parseFloat(filtres.note.value) : 0;
  var filtrePlaces = filtres.places && filtres.places.value !== '' ? parseInt(filtres.places.value, 10) : 0;
  var filtreDate = filtres.date ? filtres.date.value : '';
  var filtreDureeHeures = filtres.duree && filtres.duree.value !== '' ? parseFloat(filtres.duree.value) : Infinity;
  var filtreDureeMinutes = isFinite(filtreDureeHeures) ? filtreDureeHeures * 60 : Infinity;

  var trajetsVisibles = 0;

  for (var i = 0; i < trajetsCards.length; i++) {
    var carte = trajetsCards[i];
    var correspondRecherche = !rechercheCourante;
    if (rechercheCourante) {
      correspondRecherche = (carte.dataset.depart === rechercheCourante.depart && carte.dataset.arrivee === rechercheCourante.arrivee);
    }

    var estEcologique = carte.dataset.ecologique === 'true';
    var prix = parseFloat(carte.dataset.prix);
    var note = parseFloat(carte.dataset.note);
    var places = parseInt(carte.dataset.places, 10);
    var dateTrajet = carte.dataset.date;
    var dureeTrajet = carte.dataset.duree ? parseFloat(carte.dataset.duree) : NaN;

    var passeFiltres = correspondRecherche;
    if (passeFiltres && filtreEco && !estEcologique) {
      passeFiltres = false;
    }
    if (passeFiltres && prix > filtrePrix) {
      passeFiltres = false;
    }
    if (passeFiltres && note < filtreNote) {
      passeFiltres = false;
    }
    if (passeFiltres && places < filtrePlaces) {
      passeFiltres = false;
    }
    if (passeFiltres && filtreDate && dateTrajet !== filtreDate) {
      passeFiltres = false;
    }
    if (passeFiltres && !isNaN(dureeTrajet) && dureeTrajet > filtreDureeMinutes) {
      passeFiltres = false;
    }

    if (passeFiltres) {
      carte.style.display = '';
      trajetsVisibles++;
    } else {
      carte.style.display = 'none';
    }
  }

  if (trajetsVisibles === 0 && filtreDate && rechercheCourante) {
    var alternative = trouverDatesAlternatives(rechercheCourante.depart, rechercheCourante.arrivee, filtreDate);
    var containerMessage = document.createElement('div');
    containerMessage.className = 'col-12';
    containerMessage.dataset.message = 'true';

    if (alternative) {
      containerMessage.className += ' mb-4';
      containerMessage.innerHTML = '<div class="alert alert-info"><h5>Aucun trajet disponible pour le ' + formatDateFr(filtreDate) + '.</h5><p>Voici des trajets disponibles pour le <strong>' + formatDateFr(alternative.date) + '</strong> :</p></div>';
      container.prepend(containerMessage);

      for (var j = 0; j < alternative.trajets.length; j++) {
        var trajetSuggestion = alternative.trajets[j];
        var conducteurSuggestion = null;

        for (var k = 0; k < conducteursEtTrajets.length && !conducteurSuggestion; k++) {
          var dataConducteur = conducteursEtTrajets[k];
          for (var l = 0; l < dataConducteur.trajets.length; l++) {
            if (dataConducteur.trajets[l].id === trajetSuggestion.id) {
              conducteurSuggestion = dataConducteur.conducteur;
              break;
            }
          }
        }

        if (conducteurSuggestion) {
          var carteSuggestion = creerCarteTrajet(conducteurSuggestion, trajetSuggestion, true, true);
          container.appendChild(carteSuggestion);
        } else {
          console.log('Impossible de trouver le conducteur pour la suggestion', trajetSuggestion);
        }
      }
    } else {
      containerMessage.innerHTML = '<div class="alert alert-warning">Aucun trajet disponible pour le ' + formatDateFr(filtreDate) + ' ou les dates suivantes.</div>';
      container.prepend(containerMessage);
    }
  }
}

function initialiserPageTrajets() {
  var container = document.getElementById('trajets-container');

  if (!container) {
    console.log('Pas de conteneur pour les trajets.');
    return;
  }

  var titreRecherche = document.getElementById('titre-recherche');
  var filtres = {
    eco: document.getElementById('filtreEco'),
    prix: document.getElementById('filtrePrix'),
    note: document.getElementById('filtreNote'),
    places: document.getElementById('filtrePlaces'),
    date: document.getElementById('filtreDate'),
    duree: document.getElementById('filtreDuree')
  };

  afficherTousLesTrajets(container);

  var params = {};
  var queryString = window.location.search;
  if (queryString && queryString.indexOf('?') === 0) {
    queryString = queryString.substring(1);
    var couples = queryString.split('&');
    for (var q = 0; q < couples.length; q++) {
      var couple = couples[q];
      if (couple.trim() === '') {
        continue;
      }
      var parts = couple.split('=');
      var cle = parts[0];
      var valeur = parts.length > 1 ? parts[1] : '';
      try {
        params[cle] = decodeURIComponent(valeur.replace(/\+/g, ' '));
      } catch (err) {
        console.log('Impossible de décoder le morceau', couple, err);
        params[cle] = valeur;
      }
    }
  }

  var departParam = params.depart || null;
  var arriveeParam = params.arrivee || null;
  var dateParam = params.date || null;

  if (departParam && arriveeParam) {
    rechercheCourante = { depart: departParam, arrivee: arriveeParam };
    if (titreRecherche) {
      var detailDate = '';
      if (dateParam) {
        detailDate = ' le ' + formatDateFr(dateParam);
      }
      titreRecherche.textContent = 'Trajets de ' + departParam + ' à ' + arriveeParam + detailDate;
    }
    if (dateParam && filtres.date) {
      filtres.date.value = dateParam;
    }
  } else {
    rechercheCourante = null;
    if (titreRecherche) {
      titreRecherche.textContent = 'Tous les trajets';
    }
  }

  appliquerFiltres(container, filtres);

  var listeners = [
    { element: filtres.eco, event: 'change' },
    { element: filtres.prix, event: 'input' },
    { element: filtres.note, event: 'change' },
    { element: filtres.places, event: 'change' },
    { element: filtres.date, event: 'change' },
    { element: filtres.duree, event: 'input' }
  ];

  for (var i = 0; i < listeners.length; i++) {
    var listenerInfo = listeners[i];
    if (listenerInfo.element) {
      listenerInfo.element.addEventListener(listenerInfo.event, function () {
        appliquerFiltres(container, filtres);
      });
    }
  }
}

document.addEventListener('DOMContentLoaded', function () {
  initialiserPageTrajets();
});
  
