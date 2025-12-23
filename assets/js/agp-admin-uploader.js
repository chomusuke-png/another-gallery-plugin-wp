/**
 * Maneja la selección, ordenamiento y borrado de imágenes en el admin.
 */
jQuery(document).ready(function($) {
    var mediaUploader;
    var $container = $('#agp-preview-container');
    var $inputId   = $('#agp_image_ids');

    // 1. Inicializar Sortable (Drag & Drop)
    if ($container.length) {
        $container.sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                agpRefreshIds(); // Actualizar input al soltar
            }
        });
    }

    // 2. Abrir el Media Uploader
    $('#agp-upload-btn').on('click', function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Añadir imágenes a la galería',
            button: { text: 'Añadir a la galería' },
            multiple: true
        });

        // Al seleccionar imágenes
        mediaUploader.on('select', function() {
            var selection = mediaUploader.state().get('selection');

            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                
                // Evitar duplicados visuales si ya existe (opcional, por ahora permitimos todo)
                // Renderizar HTML
                var html = `
                    <div class="agp-img-wrapper" data-id="${attachment.id}">
                        <img src="${attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url}" />
                        <span class="agp-remove-img" title="Eliminar">&times;</span>
                    </div>
                `;
                $container.append(html);
            });

            agpRefreshIds(); // Actualizar input
        });

        mediaUploader.open();
    });

    // 3. Delegación de evento para el botón "Eliminar" (X)
    $container.on('click', '.agp-remove-img', function() {
        $(this).parent('.agp-img-wrapper').remove();
        agpRefreshIds();
    });

    /**
     * Escanea el DOM actual para reconstruir la lista de IDs en el input oculto.
     * Esto asegura que el orden visual coincida con lo que se guarda.
     */
    function agpRefreshIds() {
        var ids = [];
        $container.find('.agp-img-wrapper').each(function() {
            var id = $(this).attr('data-id');
            if (id) {
                ids.push(id);
            }
        });
        $inputId.val(ids.join(','));
    }
});