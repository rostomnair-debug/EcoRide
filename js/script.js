'use strict';


var villesFrancaises = [
    { nom: "Paris", code: "75" },
    { nom: "Marseille", code: "13" },
    { nom: "Lyon", code: "69" },
    { nom: "Toulouse", code: "31" },
    { nom: "Nice", code: "06" },
    { nom: "Nantes", code: "44" },
    { nom: "Montpellier", code: "34" },
    { nom: "Strasbourg", code: "67" },
    { nom: "Bordeaux", code: "33" },
    { nom: "Lille", code: "59" },
    { nom: "Rennes", code: "35" },
    { nom: "Reims", code: "51" },
    { nom: "Le Havre", code: "76" },
    { nom: "Saint-Étienne", code: "42" },
    { nom: "Toulon", code: "83" },
    { nom: "Grenoble", code: "38" },
    { nom: "Dijon", code: "21" },
    { nom: "Angers", code: "49" },
    { nom: "Nîmes", code: "30" },
    { nom: "Villeurbanne", code: "69" },
    { nom: "Clermont-Ferrand", code: "63" },
    { nom: "Aix-en-Provence", code: "13" },
    { nom: "Brest", code: "29" },
    { nom: "Limoges", code: "87" },
    { nom: "Tours", code: "37" },
    { nom: "Amiens", code: "80" },
    { nom: "Perpignan", code: "66" },
    { nom: "Metz", code: "57" },
    { nom: "Besançon", code: "25" },
    { nom: "Orléans", code: "45" },
    { nom: "Mulhouse", code: "68" },
    { nom: "Caen", code: "14" },
    { nom: "Rouen", code: "76" }
];

function remplirSelecteursVilles() {
    var departSelect = document.getElementById('depart');
    var arriveeSelect = document.getElementById('arrivee');

    if (!departSelect || !arriveeSelect) {
        console.log('Je ne trouve pas les listes déroulantes');
        return;
    }

    for (var i = 0; i < villesFrancaises.length; i++) {
        var ville = villesFrancaises[i];

        var optionDepart = document.createElement('option');
        optionDepart.value = ville.nom;
        optionDepart.textContent = ville.nom;
        departSelect.appendChild(optionDepart);

        var optionArrivee = document.createElement('option');
        optionArrivee.value = ville.nom;
        optionArrivee.textContent = ville.nom;
        arriveeSelect.appendChild(optionArrivee);
    }
}

function brancherFormulaire() {
    var formulaire = document.querySelector('form');

    if (!formulaire) {
        console.log('Pas de formulaire trouvé sur la page');
        return;
    }

    formulaire.addEventListener('submit', function (e) {
        e.preventDefault();

        var depart = document.getElementById('depart').value;
        var arrivee = document.getElementById('arrivee').value;
        var date = document.getElementById('date').value;

        if (depart === '' || arrivee === '' || date === '') {
            alert('Merci de choisir un départ, une arrivée et une date avant de lancer la recherche.');
            return;
        }

        window.location.href = 'covoiturages.html?depart=' + encodeURIComponent(depart) + '&arrivee=' + encodeURIComponent(arrivee) + '&date=' + encodeURIComponent(date);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    remplirSelecteursVilles();
    brancherFormulaire();
});
