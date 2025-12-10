'use strict'; 


var avisEnAttente = [
  {
    id: 'AV-301',
    chauffeurPseudo: 'Alex_75',
    chauffeurEmail: 'alex.martin@example.com',
    auteurPseudo: 'Lola_B',
    auteurEmail: 'lola.brun@example.com',
    note: 5,
    commentaire: 'Super trajet, chauffeur ponctuel et très sympathique !'
  },
  {
    id: 'AV-302',
    chauffeurPseudo: 'MarieL',
    chauffeurEmail: 'marie.lambert@example.com',
    auteurPseudo: 'Tommy33',
    auteurEmail: 'tommy.guillot@example.com',
    note: 3,
    commentaire: 'Trajet correct mais un peu à l\'étroit à l\'arrière.'
  },
  {
    id: 'AV-303',
    chauffeurPseudo: 'LeoSud',
    chauffeurEmail: 'leo.martinez@example.com',
    auteurPseudo: 'Sisi93',
    auteurEmail: 'sarah.simon@example.com',
    note: 2,
    commentaire: 'Retard de 30 minutes au départ, à surveiller.'
  }
];

var incidentsSignales = [
  {
    numero: 'TR-2025-018',
    conducteurPseudo: 'PierreT',
    conducteurEmail: 'pierre.tremblay@example.com',
    passagerPseudo: 'MilaP',
    passagerEmail: 'mila.perrin@example.com',
    lieuDepart: 'Paris',
    lieuArrivee: 'Lyon',
    dateDepart: '2025-02-12 08:00',
    dateArrivee: '2025-02-12 12:45',
    resume: 'Conflit sur la pause prévue pendant le trajet. Le passager signale un échange tendu.'
  },
  {
    numero: 'TR-2025-021',
    conducteurPseudo: 'JulieEco',
    conducteurEmail: 'julie.dupont@example.com',
    passagerPseudo: 'MarcH',
    passagerEmail: 'marc.heraud@example.com',
    lieuDepart: 'Bordeaux',
    lieuArrivee: 'Toulouse',
    dateDepart: '2025-02-18 09:30',
    dateArrivee: '2025-02-18 12:20',
    resume: 'Le passager déclare que la climatisation ne fonctionnait pas et qu\'il a eu un malaise.'
  }
];

function afficherAvisEnAttente() {
  var conteneurAvis = document.getElementById('liste-avis');
  var historique = document.getElementById('historique-decisions');

  if (!conteneurAvis) {
    return;
  }

  conteneurAvis.innerHTML = '';

  if (avisEnAttente.length === 0) {
    var message = document.createElement('div');
    message.className = 'alert alert-info mb-0';
    message.textContent = 'Aucun avis en cours de modération pour le moment.';
    conteneurAvis.appendChild(message);
    return;
  }

  for (var i = 0; i < avisEnAttente.length; i++) {
    var avis = avisEnAttente[i];
    var carte = document.createElement('div');
    carte.className = 'border rounded-3 p-3';

    var contenu = '';
    contenu += '<div class="d-flex justify-content-between align-items-start">';
    contenu += '<div>';
    contenu += '<p class="mb-1"><strong>Fiche ' + avis.id + '</strong> pour <span class="text-success">' + avis.chauffeurPseudo + '</span></p>';
    contenu += '<p class="mb-1 text-muted">Avis rédigé par ' + avis.auteurPseudo + ' (' + avis.auteurEmail + ')</p>';
    contenu += '<p class="mb-1">Note proposée : <strong>' + avis.note + '/5</strong></p>';
    contenu += '<p class="mb-2">"' + avis.commentaire + '"</p>';
    contenu += '</div>';
    contenu += '</div>';
    carte.innerHTML = contenu;

    var zoneBoutons = document.createElement('div');
    zoneBoutons.className = 'd-flex gap-2';

    var boutonValider = document.createElement('button');
    boutonValider.className = 'btn btn-sm btn-success';
    boutonValider.textContent = 'Valider l\'avis';
    boutonValider.addEventListener('click', (function (idAvis) {
      return function () {
        prendreDecisionSurAvis(idAvis, 'validé');
      };
    })(avis.id));

    var boutonRefuser = document.createElement('button');
    boutonRefuser.className = 'btn btn-sm btn-outline-danger';
    boutonRefuser.textContent = 'Refuser l\'avis';
    boutonRefuser.addEventListener('click', (function (idAvis) {
      return function () {
        prendreDecisionSurAvis(idAvis, 'refusé');
      };
    })(avis.id));

    zoneBoutons.appendChild(boutonValider);
    zoneBoutons.appendChild(boutonRefuser);
    carte.appendChild(zoneBoutons);
    conteneurAvis.appendChild(carte);
  }

  if (historique && historique.children.length === 0) {
    var info = document.createElement('li');
    info.className = 'list-group-item small text-muted';
    info.textContent = 'Les décisions prises apparaîtront ici.';
    historique.appendChild(info);
  }
}

function prendreDecisionSurAvis(idAvis, decision) {
  var avisTrouve = null;
  var index = -1;

  for (var i = 0; i < avisEnAttente.length; i++) {
    if (avisEnAttente[i].id === idAvis) {
      avisTrouve = avisEnAttente[i];
      index = i;
      break;
    }
  }

  if (!avisTrouve || index === -1) {
    console.log('Avis introuvable :', idAvis);
    return;
  }

  avisEnAttente.splice(index, 1);
  ajouterHistoriqueDecision(avisTrouve, decision);
  afficherAvisEnAttente();
}

function ajouterHistoriqueDecision(avis, decision) {
  var historique = document.getElementById('historique-decisions');
  if (!historique) {
    return;
  }

  if (historique.children.length === 1 && historique.children[0].classList.contains('text-muted')) {
    historique.innerHTML = '';
  }

  var element = document.createElement('li');
  element.className = 'list-group-item d-flex justify-content-between align-items-start';

  var cadrage = '';
  cadrage += '<div>';
  cadrage += '<strong>Avis ' + avis.id + '</strong> sur ' + avis.chauffeurPseudo + '<br>';
  cadrage += 'Auteur : ' + avis.auteurPseudo + ' - ' + avis.auteurEmail;
  cadrage += '</div>';
  cadrage += '<span class="badge ' + (decision === 'validé' ? 'bg-success' : 'bg-danger') + ' text-uppercase">' + decision + '</span>';

  element.innerHTML = cadrage;
  historique.prepend(element);
}

function afficherIncidents() {
  var conteneurIncidents = document.getElementById('liste-incidents');
  if (!conteneurIncidents) {
    return;
  }

  conteneurIncidents.innerHTML = '';

  if (incidentsSignales.length === 0) {
    var message = document.createElement('div');
    message.className = 'alert alert-success mb-0';
    message.textContent = 'Aucun signalement en cours, bravo !';
    conteneurIncidents.appendChild(message);
    return;
  }

  for (var i = 0; i < incidentsSignales.length; i++) {
    var incident = incidentsSignales[i];
    var carte = document.createElement('div');
    carte.className = 'border rounded-3 p-3 bg-light';

    var contenu = '';
    contenu += '<p class="mb-1"><strong>Dossier ' + incident.numero + '</strong></p>';
    contenu += '<p class="mb-1">Conducteur : ' + incident.conducteurPseudo + ' (' + incident.conducteurEmail + ')</p>';
    contenu += '<p class="mb-1">Passager : ' + incident.passagerPseudo + ' (' + incident.passagerEmail + ')</p>';
    contenu += '<p class="mb-1">Trajet : ' + incident.lieuDepart + ' → ' + incident.lieuArrivee + '</p>';
    contenu += '<p class="mb-1">Départ : ' + incident.dateDepart + '</p>';
    contenu += '<p class="mb-1">Arrivée : ' + incident.dateArrivee + '</p>';
    contenu += '<p class="mb-0 text-muted">' + incident.resume + '</p>';

    carte.innerHTML = contenu;
    conteneurIncidents.appendChild(carte);
  }
}

document.addEventListener('DOMContentLoaded', function () {
  afficherAvisEnAttente();
  afficherIncidents();
});
