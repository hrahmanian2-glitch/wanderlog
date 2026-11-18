const likeBtn = document.getElementById('like-btn');
const likeCount = document.getElementById('like-count');
const commentForm = document.getElementById('comment-form');
const commentInput = document.getElementById('comment-input');
const commentsList = document.getElementById('comments-list');
const commentCount = document.getElementById('comment-count');

const tripId = likeBtn.dataset.tripId;

const commentairesAffiches = new Set();
document.querySelectorAll('.comment').forEach(function(div) {
    if (div.dataset.commentId) {
        commentairesAffiches.add(parseInt(div.dataset.commentId));
    }
});

let dernierUpdate = Math.floor(Date.now() / 1000);


likeBtn.addEventListener('click', function() {
    likeBtn.disabled = true;
    const body = 'trip_id=' + encodeURIComponent(tripId);

    fetch('like.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.ok) {
            likeCount.textContent = data.count;
            if (data.liked) likeBtn.classList.add('liked');
            else likeBtn.classList.remove('liked');
        } else {
            alert('Erreur : ' + data.erreur);
        }
    })
    .catch(function(err) { alert('Erreur réseau : ' + err.message); })
    .finally(function() { likeBtn.disabled = false; });
});


commentForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const contenu = commentInput.value.trim();
    if (contenu === '') return;

    const submitBtn = commentForm.querySelector('button');
    submitBtn.disabled = true;
    commentInput.disabled = true;

    const body = 'trip_id=' + encodeURIComponent(tripId)
               + '&contenu=' + encodeURIComponent(contenu);

    fetch('comment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.ok) {
            ajouterCommentaireDansLaListe(data.commentaire);
            commentInput.value = '';
            commentCount.textContent = parseInt(commentCount.textContent) + 1;
        } else {
            alert('Erreur : ' + data.erreur);
        }
    })
    .catch(function(err) { alert('Erreur réseau : ' + err.message); })
    .finally(function() {
        submitBtn.disabled = false;
        commentInput.disabled = false;
        commentInput.focus();
    });
});


setInterval(function() {
    fetch('events.php?since=' + dernierUpdate + '&trip_id=' + tripId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.ok) return;

            if (data.comments && data.comments.length > 0) {
                data.comments.forEach(function(c) {
                    ajouterCommentaireDansLaListe(c);
                    commentCount.textContent = parseInt(commentCount.textContent) + 1;
                });
            }

            if (data.likes) {
                if (parseInt(likeCount.textContent) !== data.likes.count) {
                    likeCount.textContent = data.likes.count;
                    likeCount.classList.remove('like-pulse');
                    void likeCount.offsetWidth;
                    likeCount.classList.add('like-pulse');
                }
            }

            dernierUpdate = data.now;
        })
        .catch(function(err) { console.error('Erreur polling :', err); });
}, 3000);


function ajouterCommentaireDansLaListe(c) {
    if (commentairesAffiches.has(c.id)) return;
    commentairesAffiches.add(c.id);

    const div = document.createElement('div');
    div.className = 'comment';
    div.dataset.commentId = c.id;

    const header = document.createElement('div');
    header.className = 'comment-header';

    const auteur = document.createElement('strong');
    auteur.textContent = c.auteur;

    const date = document.createElement('span');
    date.className = 'comment-date';
    date.textContent = formaterDate(c.created_at);

    header.appendChild(auteur);
    header.appendChild(date);

    const contenu = document.createElement('p');
    contenu.className = 'comment-content';
    contenu.textContent = c.contenu;

    div.appendChild(header);
    div.appendChild(contenu);

    commentsList.appendChild(div);
}

function formaterDate(timestamp) {
    const d = new Date(timestamp * 1000);
    const jj = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const aaaa = d.getFullYear();
    const hh = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    return jj + '/' + mm + '/' + aaaa + ' à ' + hh + ':' + min;
}
