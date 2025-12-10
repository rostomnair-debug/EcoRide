// pour éviter de choisir deux fois la même ville

document.addEventListener('DOMContentLoaded', function () {
    var departSelect = document.getElementById('depart');
    var arriveeSelect = document.getElementById('arrivee');

    if (!departSelect || !arriveeSelect) {
        console.log('Les sélecteurs de villes ne sont pas prêts, je laisse tomber.');
        return;
    }

    departSelect.addEventListener('change', function () {
        var valeurChoisie = departSelect.value;

        for (var i = 0; i < arriveeSelect.options.length; i++) {
            var option = arriveeSelect.options[i];
            if (option.value === valeurChoisie && valeurChoisie !== '') {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        }

        if (arriveeSelect.value === valeurChoisie) {
            arriveeSelect.selectedIndex = 0;
        }
    });

    arriveeSelect.addEventListener('change', function () {
        var valeurChoisie = arriveeSelect.value;

        for (var j = 0; j < departSelect.options.length; j++) {
            var optionDepart = departSelect.options[j];
            if (optionDepart.value === valeurChoisie && valeurChoisie !== '') {
                optionDepart.disabled = true;
            } else {
                optionDepart.disabled = false;
            }
        }

        if (departSelect.value === valeurChoisie) {
            departSelect.selectedIndex = 0;
        }
    });
});
