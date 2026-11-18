const map = L.map('map-picker').setView([46, 2], 3);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
    maxZoom: 18
}).addTo(map);

let marker = null;

const inputLat = document.getElementById('input-lat');
const inputLng = document.getElementById('input-lng');
const coordDisplay = document.getElementById('coord-display');

map.on('click', function(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;

    if (marker !== null) {
        map.removeLayer(marker);
    }

    marker = L.marker([lat, lng]).addTo(map);

    inputLat.value = lat.toFixed(6);
    inputLng.value = lng.toFixed(6);

    coordDisplay.textContent =
        'Emplacement choisi : ' + lat.toFixed(4) + ', ' + lng.toFixed(4);
    coordDisplay.classList.add('coord-display-set');
});
