document.addEventListener('DOMContentLoaded', function() {

    const champs = document.querySelectorAll('input, textarea, select');

    champs.forEach(function(champ) {

        champ.addEventListener('invalid', function(e) {
            if (champ.validity.valueMissing) {
                champ.setCustomValidity('Veuillez remplir ce champ.');
            }
            else if (champ.validity.typeMismatch && champ.type === 'email') {
                champ.setCustomValidity('Veuillez entrer une adresse email valide.');
            }
            else if (champ.validity.tooShort) {
                champ.setCustomValidity('Ce champ doit faire au moins ' + champ.minLength + ' caractères.');
            }
            else {
                champ.setCustomValidity('Champ invalide.');
            }
        });

        champ.addEventListener('input', function() {
            champ.setCustomValidity('');
        });
    });
});
