'use strict';

document.addEventListener('DOMContentLoaded', function () {
  var chartCanvases = [
    document.getElementById('ridesByCityChart'),
    document.getElementById('ecoSplitChart'),
    document.getElementById('monthlyGrowthChart')
  ];

  var hasMissingCanvas = chartCanvases.some(function (canvas) {
    return !(canvas instanceof HTMLCanvasElement);
  });

  if (hasMissingCanvas) {
    console.warn('Graphiques non initialisés : canvas manquant.');
    return;
  }

  if (typeof window.Chart !== 'function') {
    chartCanvases.forEach(showChartFallback);
    console.error('Chart.js est introuvable, les graphiques ne seront pas affichés.');
    return;
  }

  var ridesByCityCtx = chartCanvases[0].getContext('2d');
  var ecoSplitCtx = chartCanvases[1].getContext('2d');
  var monthlyGrowthCtx = chartCanvases[2].getContext('2d');

  if (!(ridesByCityCtx && ecoSplitCtx && monthlyGrowthCtx)) {
    console.warn('Graphiques non initialisés : contexte 2D introuvable.');
    return;
  }

  // Barres des trajets confirmés par ville
  new Chart(ridesByCityCtx, {
    type: 'bar',
    data: {
      labels: ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nantes', 'Bordeaux'],
      datasets: [{
        label: 'Trajets confirmés',
        data: [42, 36, 28, 24, 19, 21],
        backgroundColor: 'rgba(25, 135, 84, 0.7)',
        borderRadius: 8
      }]
    },
    options: {
      animation: false,
      responsive: true,
      maintainAspectRatio: false,
      resizeDelay: 200,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: '#198754'
        }
      }
    }
  });

  // Camembert pour la répartition écologique
  new Chart(ecoSplitCtx, {
    type: 'doughnut',
    data: {
      labels: ['Électrique & hybride', 'Thermique'],
      datasets: [{
        label: 'Répartition écologique',
        data: [60, 40],
        backgroundColor: [
          'rgba(13, 110, 253, 0.8)',
          'rgba(255, 193, 7, 0.8)',
        ],
        borderWidth: 0
      }]
    },
    options: {
      animation: false,
      responsive: true,
      maintainAspectRatio: false,
      resizeDelay: 200,
      cutout: '65%',
      plugins: {
        legend: {
          position: 'right',
          labels: {
            usePointStyle: true
          }
        }
      }
    }
  });

  // Ligne pour la croissance des inscriptions
  new Chart(monthlyGrowthCtx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'],
      datasets: [{
        label: 'Inscriptions',
        data: [52, 48, 55, 62, 70, 118, 132, 92, 85, 78, 90, 101],
        fill: true,
        tension: 0.35,
        borderColor: 'rgba(255, 193, 7, 1)',
        backgroundColor: 'rgba(255, 193, 7, 0.25)',
        pointBackgroundColor: '#ffc107',
        pointRadius: 4
      }]
    },
    options: {
      animation: false,
      responsive: true,
      maintainAspectRatio: false,
      resizeDelay: 200,
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
});

function showChartFallback(canvas) {
  if (!canvas) {
    return;
  }

  var fallbackMessage = canvas.dataset.chartFallback || 'Graphique momentanément indisponible.';
  var fallbackContainer = document.createElement('div');
  fallbackContainer.className = 'alert alert-warning text-center py-4 mb-0';
  fallbackContainer.setAttribute('role', 'alert');
  fallbackContainer.textContent = fallbackMessage;

  canvas.replaceWith(fallbackContainer);
}
