/**
 * Lógica simple para el Lightbox de la galería.
 */
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("agp-lightbox");
    var modalImg = document.getElementById("agp-img-full");
    var triggers = document.querySelectorAll(".agp-lightbox-trigger");
    var closeSpan = document.getElementsByClassName("agp-close")[0];

    if (!modal) return;

    triggers.forEach(function(img) {
        img.onclick = function() {
            modal.style.display = "block";
            modalImg.src = this.getAttribute('data-full');
        }
    });

    closeSpan.onclick = function() {
        modal.style.display = "none";
    }

    modal.onclick = function(e) {
        if(e.target === modal) {
            modal.style.display = "none";
        }
    }
});