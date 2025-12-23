document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos por ID según el HTML generado en el shortcode
    var modal = document.getElementById("agp-modal");
    var modalImg = document.getElementById("agp-modal-img");
    var triggers = document.querySelectorAll(".agp-lightbox-trigger");
    var closeSpan = document.querySelector(".agp-close");

    if (!modal) return;

    // Abrir modal
    triggers.forEach(function(img) {
        img.addEventListener('click', function() {
            var fullUrl = this.getAttribute('data-full');
            if (fullUrl) {
                modalImg.src = fullUrl;
                modal.classList.add('show'); // Añadimos clase para activar el CSS Flexbox
            }
        });
    });

    // Función cerrar
    function closeModal() {
        modal.classList.remove('show');
        setTimeout(function(){
            modalImg.src = ''; // Limpiar src al cerrar
        }, 300);
    }

    // Cerrar con la X
    if (closeSpan) {
        closeSpan.addEventListener('click', closeModal);
    }

    // Cerrar al hacer clic fuera de la imagen
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Cerrar con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape" && modal.classList.contains('show')) {
            closeModal();
        }
    });
});