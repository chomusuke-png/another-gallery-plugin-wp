jQuery(document).ready(function($) {
    var mediaUploader;
    var $container = $('#agp-preview-container');
    var $inputId   = $('#agp_image_ids');

    // Función para comprobar si está vacío
    function checkEmptyState() {
        // Contamos cuántos wrappers de imagen hay
        if ($container.find('.agp-img-wrapper').length === 0) {
            $container.addClass('agp-is-empty');
        } else {
            $container.removeClass('agp-is-empty');
        }
    }

    // Inicializar Sortable
    if ($container.length) {
        $container.sortable({
            placeholder: "ui-state-highlight", // Clase nativa de WP
            items: '.agp-img-wrapper', // Solo ordenar items, ignorar placeholder
            update: function(event, ui) {
                agpRefreshIds();
            }
        });
        
        // Chequeo inicial al cargar la página
        checkEmptyState();
    }

    $('#agp-upload-btn').on('click', function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Gestionar Galería',
            button: { text: 'Insertar en la galería' },
            multiple: true
        });

        mediaUploader.on('select', function() {
            var selection = mediaUploader.state().get('selection');
            
            // Quitamos clase vacío inmediatamente
            $container.removeClass('agp-is-empty');

            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                
                // Evitamos añadir duplicados visuales si ya existen (opcional)
                if ($container.find('.agp-img-wrapper[data-id="' + attachment.id + '"]').length > 0) {
                    return;
                }

                var thumb = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                
                var html = `
                    <div class="agp-img-wrapper" data-id="${attachment.id}">
                        <img src="${thumb}" />
                        <div class="agp-img-actions">
                            <span class="agp-remove-img" title="Eliminar">&times;</span>
                        </div>
                    </div>
                `;
                $container.append(html);
            });

            agpRefreshIds();
            checkEmptyState(); // Verificar por seguridad
        });

        mediaUploader.open();
    });

    // Eliminar imagen
    $container.on('click', '.agp-remove-img', function() {
        $(this).closest('.agp-img-wrapper').remove();
        agpRefreshIds();
        checkEmptyState(); // Si borramos la última, mostrar placeholder
    });

    function agpRefreshIds() {
        var ids = [];
        $container.find('.agp-img-wrapper').each(function() {
            var id = $(this).attr('data-id');
            if (id) ids.push(id);
        });
        $inputId.val(ids.join(','));
    }
});