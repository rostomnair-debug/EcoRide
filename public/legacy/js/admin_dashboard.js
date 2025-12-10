'use strict';

(function initLegacyAdminCharts() {
  var init = function () {
    var ids = ['ridesByCityChart', 'ecoSplitChart', 'monthlyGrowthChart'];
    var canvases = ids.map(function (id) { return document.getElementById(id); });
    if (canvases.some(function (c) { return !(c instanceof HTMLCanvasElement); })) return;
    if (typeof window.Chart !== 'function') { canvases.forEach(showChartFallback); return; }

    var ridesByCityCtx = canvases[0].getContext('2d');
    var ecoSplitCtx = canvases[1].getContext('2d');
    var monthlyGrowthCtx = canvases[2].getContext('2d');

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
      options: { animation: false, responsive: true, maintainAspectRatio: false, resizeDelay: 150, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false } } }
    });

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
      options: { animation: false, responsive: true, maintainAspectRatio: false, resizeDelay: 150, cutout: '65%', plugins: { legend: { position: 'right', labels: { usePointStyle: true } } } }
    });

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
      options: { animation: false, responsive: true, maintainAspectRatio: false, resizeDelay: 150, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });
  };

  if (document.readyState === 'complete') {
    init();
  } else {
    document.addEventListener('DOMContentLoaded', init);
    window.addEventListener('load', init);
  }
})();

function showChartFallback(canvas) {
  if (!canvas) return;
  var msg = canvas.dataset.chartFallback || 'Graphique momentanément indisponible.';
  var fallback = document.createElement('div');
  fallback.className = 'alert alert-warning text-center py-4 mb-0';
  fallback.setAttribute('role', 'alert');
  fallback.textContent = msg;
  canvas.replaceWith(fallback);
}
