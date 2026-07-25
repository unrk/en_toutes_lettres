(function () {
    'use strict';

    function synchroniser(editeur) {
        var cible = document.getElementById(editeur.dataset.cible);
        if (cible) {
            cible.value = editeur.innerHTML;
        }
    }

    document.querySelectorAll('[data-editeur-enrichi]').forEach(function (editeur) {
        editeur.addEventListener('input', function () {
            synchroniser(editeur);
        });

        var formulaire = editeur.closest('form');
        if (formulaire) {
            formulaire.addEventListener('submit', function () {
                synchroniser(editeur);
            });
        }
    });

    document.querySelectorAll('[data-barre-pour]').forEach(function (barre) {
        var editeur = document.getElementById(barre.dataset.barrePour);
        if (!editeur) {
            return;
        }

        barre.querySelectorAll('button[data-commande]').forEach(function (bouton) {
            bouton.addEventListener('click', function (evenement) {
                evenement.preventDefault();
                editeur.focus();

                var commande = bouton.dataset.commande;
                var valeur = bouton.dataset.valeur || undefined;

                if (commande === 'createLink') {
                    var url = window.prompt('Adresse du lien (https://...) :');
                    if (!url) {
                        return;
                    }
                    valeur = url;
                }

                document.execCommand(commande, false, valeur);
                synchroniser(editeur);
            });
        });
    });
})();
