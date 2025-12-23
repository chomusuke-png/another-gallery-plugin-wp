/**
 * Maneja la selección de imágenes en el admin de WordPress usando wp.media.
 */
jQuery(document).ready(function($) {
    var mediaUploader;

    $('#agp-upload-btn').on('click', function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Seleccionar Imágenes para la Galería',
            button: { text: 'Usar estas imágenes' },
            multiple: true
        });

        mediaUploader.on('select', function() {
            var selection = mediaUploader.state().get('selection');
            var ids = [];
            $('#agp-images-preview').html('');

            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                ids.push(attachment.id);
                $('#agp-images-preview').append('<img src="' + attachment.sizes.thumbnail.url + '" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ccc;">');
            });

            $('#agp_image_ids').val(ids.join(','));
        });

        mediaUploader.open();
    });
});