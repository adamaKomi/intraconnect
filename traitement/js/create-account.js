document.addEventListener('DOMContentLoaded', function () {
    // Récupérer le formulaire
    var form = document.querySelector('form');

    // Ajouter un écouteur d'événement pour le soumission du formulaire
    form.addEventListener('submit', function (event) {
        // Empêcher le comportement par défaut du formulaire
        event.preventDefault();

        // Valider le formulaire
        if (!form.checkValidity()) {
            // Si le formulaire est invalide, arrêter l'exécution
            return;
        }

        // Si le formulaire est valide, soumettre le formulaire
        form.submit();

        // Vérifier si la case "addAdmin" est cochée
        var addAdminCheckbox = document.querySelector('input[name="addAdmin"]');
        if (addAdminCheckbox.checked) {
            // Si oui, ajouter l'utilisateur comme administrateur
            // Vous pouvez envoyer une requête au serveur ici pour effectuer cette opération
            // Par exemple :
            // fetch('traitement/add-admin.php', {
            //     method: 'POST',
            //     body: new FormData(form)
            // })
            // .then(response => {
            //     if (response.ok) {
            //         // Afficher un message de succès ou effectuer d'autres actions
            //     } else {
            //         // Afficher un message d'erreur ou effectuer d'autres actions
            //     }
            // })
            // .catch(error => console.error('Erreur lors de l\'ajout de l\'administrateur:', error));
        }
    });

    // Ajouter un écouteur d'événement pour les changements de la case à cocher "acceptTerms"
    var acceptTermsCheckbox = document.getElementById('acceptTerms');
    acceptTermsCheckbox.addEventListener('change', function () {
        // Mettre à jour l'état de validation de la case à cocher "acceptTerms"
        acceptTermsCheckbox.setCustomValidity(this.checked ? '' : 'Vous devez accepter avant de soumettre.');
    });
});
