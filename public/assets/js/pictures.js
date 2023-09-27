let links = document.querySelectorAll("[data-delete]");

// Bouler sur les liens de suppression
for (let link of links){
    //Ecouteur d'evenement
    link.addEventListener("click", function (e) {
        // Empêcher la navigation
        e.preventDefault();

        // Demande de confirmation
        if (confirm("Voulez-vous supprimer cette image ?")){
            // Envoyer le requête Ajax
            fetch(this.getAttribute("href"), {
                method: "DELETE",
                headers: {
                    "X-Request-With": "XMLHttpRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({"_token": this.dataset.token})
            }).then(response=>response.json())
                .then(data => {
                    if (data.success){
                        this.parentElement.remove();
                    } else {
                        alert(data.error);
                    }
                })
        }
    })
}