'use strict';

(function initAdminCharts() {
  var init = function () {
    var mapIds = {
      trips: 'chartTrips',
      revenue: 'chartRevenue',
      ridesByCity: 'chartRidesByCity',
      ecoSplit: 'chartEcoSplit',
      monthly: 'chartMonthly'
    };

    var canvases = {};
    Object.keys(mapIds).forEach(function (key) {
      canvases[key] = document.getElementById(mapIds[key]);
    });

    var missing = Object.values(canvases).some(function (c) { return !(c instanceof HTMLCanvasElement); });
    if (missing) {
      return;
    }

    if (typeof window.Chart !== 'function') {
      Object.values(canvases).forEach(showChartFallback);
      return;
    }

    var optsBase = {
      animation: false,
      responsive: true,
      maintainAspectRatio: false,
      resizeDelay: 150,
    };

    new Chart(canvases.trips.getContext('2d'), {
      type: 'bar',
      data: {
        labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'J-1', 'Aujourd\'hui'],
        datasets: [{
          label: 'Covoiturages',
          data: [9, 11, 7, 13, 12, 10, 8],
          backgroundColor: 'rgba(25, 135, 84, 0.75)',
          borderRadius: 8
        }]
      },
      options: Object.assign({}, optsBase, {
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        plugins: { legend: { display: false } }
      })
    });

    new Chart(canvases.revenue.getContext('2d'), {
      type: 'line',
      data: {
        labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'J-1', 'Aujourd\'hui'],
        datasets: [{
          label: 'Crédits générés',
          data: [48, 52, 44, 58, 61, 55, 49],
          borderColor: 'rgba(13, 110, 253, 1)',
          backgroundColor: 'rgba(13, 110, 253, 0.15)',
          tension: 0.3,
          fill: true,
          pointRadius: 4
        }]
      },
      options: Object.assign({}, optsBase, {
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
      })
    });

    new Chart(canvases.ridesByCity.getContext('2d'), {
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
      options: Object.assign({}, optsBase, {
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        plugins: { legend: { display: false } }
      })
    });

    new Chart(canvases.ecoSplit.getContext('2d'), {
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
      options: Object.assign({}, optsBase, {
        cutout: '65%',
        plugins: {
          legend: {
            position: 'right',
            labels: { usePointStyle: true }
          }
        }
      })
    });

    new Chart(canvases.monthly.getContext('2d'), {
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
      options: Object.assign({}, optsBase, {
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
      })
    });
  };

  const launch = (retry) => {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function () { init(retry); });
    } else {
      init(retry);
    }
    window.addEventListener('load', function () { init(retry); });
  };
  launch(0);
})();

function showChartFallback(canvas) {
  if (!canvas) return;
  var fallbackMessage = canvas.dataset.chartFallback || 'Graphique momentanément indisponible.';
  var fallbackContainer = document.createElement('div');
  fallbackContainer.className = 'alert alert-warning text-center py-4 mb-0';
  fallbackContainer.setAttribute('role', 'alert');
  fallbackContainer.textContent = fallbackMessage;
  canvas.replaceWith(fallbackContainer);
}
