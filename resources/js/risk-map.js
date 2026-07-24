import L from 'leaflet';

document.addEventListener('DOMContentLoaded', () => {

    const element = document.getElementById('riskMap');

    if (!element) {
        console.log('riskMap tidak ditemukan');
        return;
    }

    console.log('riskMap ditemukan');

    const map = L.map(element).setView([-2.5, 118], 5);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors, © CARTO'
    }).addTo(map);

    L.marker([-6.2, 106.8])
        .addTo(map)
        .bindPopup('Indonesia')
        .openPopup();

});