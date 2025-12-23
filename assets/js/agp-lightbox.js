document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("agp-modal");
    var modalImg = document.getElementById("agp-modal-img");
    var captionText = document.getElementById("agp-caption-text"); // Capturamos la caja de texto
    var triggers = document.querySelectorAll(".agp-lightbox-trigger");
    var closeSpan = document.querySelector(".agp-close");

    if (!modal) return;

    triggers.forEach(function(img) {
        img.addEventListener('click', function() {
            var fullUrl = this.getAttribute('data-full');
            var desc = this.getAttribute('data-desc'); // Leemos la descripción

            if (fullUrl) {
                modalImg.src = fullUrl;
                // Si hay descripción la mostramos, si no, limpiamos
                if (captionText) captionText.textContent = desc ? desc : ''; 
                modal.classList.add('show');
            }
        });
    });

    function closeModal() {
        modal.classList.remove('show');
        setTimeout(function(){
            modalImg.src = '';
            if (captionText) captionText.textContent = ''; // Limpiar texto
        }, 300);
    }

    if (closeSpan) closeSpan.addEventListener('click', closeModal);
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('agp-modal-wrapper')) {
            closeModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape" && modal.classList.contains('show')) closeModal();
    });
});