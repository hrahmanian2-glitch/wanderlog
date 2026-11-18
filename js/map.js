const map = L.map('map').setView([30, 10], 2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
    maxZoom: 18
}).addTo(map);

const voyagesAffiches = new Set();

let dernierUpdate = Math.floor(Date.now() / 1000);

VOYAGES.forEach(function(voyage) {
    afficherVoyage(voyage);
});


function afficherVoyage(voyage) {
    if (voyagesAffiches.has(voyage.id)) return;
    voyagesAffiches.add(voyage.id);

    const marker = L.marker([voyage.lat, voyage.lng]).addTo(map);

    let popupHTML = '<div class="popup">';
    popupHTML += '<h3>' + escapeHTML(voyage.titre) + '</h3>';
    popupHTML += '<p class="popup-meta">par <a href="profile.php?email=' + encodeURIComponent(voyage.email || '') + '"><strong>' + escapeHTML(voyage.auteur) + '</strong></a>';
    if (voyage.lieu) popupHTML += ' — ' + escapeHTML(voyage.lieu);
    if (voyage.date) popupHTML += ' (' + escapeHTML(voyage.date) + ')';
    popupHTML += '</p>';

    let histoire = voyage.histoire;
    if (histoire.length > 150) histoire = histoire.substring(0, 150) + '...';
    popupHTML += '<p>' + escapeHTML(histoire) + '</p>';

    if (voyage.photos && voyage.photos.length > 0) {
        popupHTML += '<img src="uploads/' + encodeURIComponent(voyage.photos[0])
                  + '" class="popup-photo" alt="Photo">';
    }

    popupHTML += '<a href="trip.php?id=' + voyage.id + '" class="popup-link">Voir plus →</a>';
    popupHTML += '</div>';

    marker.bindPopup(popupHTML);
}


setInterval(function() {
    fetch('events.php?since=' + dernierUpdate)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.ok) return;

            if (data.trips && data.trips.length > 0) {
                data.trips.forEach(function(v) {
                    afficherVoyage(v);
                    afficherNotification('Nouveau voyage : ' + v.titre + ' par ' + v.auteur);
                });
            }

            dernierUpdate = data.now;
        })
        .catch(function(err) {
            console.error('Erreur polling :', err);
        });
}, 3000);


function afficherNotification(message) {
    const notif = document.createElement('div');
    notif.className = 'notif';
    notif.textContent = message;
    document.body.appendChild(notif);

    setTimeout(function() {
        notif.classList.add('notif-fade');
        setTimeout(function() {
            notif.remove();
        }, 500);
    }, 4000);
}


function escapeHTML(str) {
    if (str === null || str === undefined) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
