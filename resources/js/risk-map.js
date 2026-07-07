import L from 'leaflet';

document.addEventListener('DOMContentLoaded', () => {

    const mapElement = document.getElementById('riskMap');

    if (!mapElement) return;

    const map = L.map('riskMap').setView([20, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const countries = [
        {
            name: 'Indonesia',
            lat: -6.2,
            lng: 106.8,
            risk: 'Medium',
            score: 45
        },
        {
            name: 'China',
            lat: 39.9,
            lng: 116.4,
            risk: 'High',
            score: 82
        },
        {
            name: 'Australia',
            lat: -35.2,
            lng: 149.1,
            risk: 'Low',
            score: 18
        }
    ];

    countries.forEach(country => {

        let color = 'green';

        if(country.risk === 'Medium'){
            color = 'orange';
        }

        if(country.risk === 'High'){
            color = 'red';
        }

        L.circleMarker([country.lat, country.lng], {

            radius: 8,
            color: color,
            fillColor: color,
            fillOpacity: 0.8

        })
        .addTo(map)
        .bindPopup(`
            <b>${country.name}</b><br>
            Risk : ${country.risk}<br>
            Score : ${country.score}
        `);

    });

});