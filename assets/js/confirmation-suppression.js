(function () {
    'use strict';

    document.querySelectorAll('form[data-confirmation]').forEach(function (formulaire) {
        formulaire.addEventListener('submit', function (evenement) {
            if (!window.confirm(formulaire.dataset.confirmation)) {
                evenement.preventDefault();
            }
        });
    });
})();
