<?php

class AGP_Metaboxes {

    /**
     * Agrega la caja de meta.
     *
     * @return void
     */
    public function add_meta_box() {
        add_meta_box(
            'agp_gallery_images',
            'Galería de Imágenes (Arrastra para reordenar)',
            [ $this, 'render_html' ],
            'agp_gallery',
            'normal',
            'high'
        );
    }

    /**
     * Renderiza el HTML del metabox con estructura para JS.
     *
     * @param WP_Post $post Objeto del post actual.
     * @return void
     */
    public function render_html( $post ) {
        wp_nonce_field( 'agp_save_action', 'agp_nonce' );
        $ids = get_post_meta( $post->ID, '_agp_image_ids', true );
        
        // Convertimos IDs a array para verificar si hay datos
        $id_array = ! empty( $ids ) ? explode( ',', $ids ) : [];
        ?>
        <div class="agp-uploader-wrap">
            <input type="hidden" id="agp_image_ids" name="agp_image_ids" value="<?php echo esc_attr( $ids ); ?>">
            
            <div id="agp-preview-container">
                <?php foreach ( $id_array as $attachment_id ) : 
                    $url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
                    if ( $url ) : ?>
                        <div class="agp-img-wrapper" data-id="<?php echo esc_attr( $attachment_id ); ?>">
                            <img src="<?php echo esc_url( $url ); ?>" alt="Gallery Image">
                            <span class="agp-remove-img" title="Eliminar imagen">&times;</span>
                        </div>
                    <?php endif; 
                endforeach; ?>
            </div>

            <p class="description">Puedes arrastrar las imágenes para cambiar el orden.</p>
            <button type="button" class="button button-primary button-large" id="agp-upload-btn">
                <span class="dashicons dashicons-images-alt2" style="margin-top:4px;"></span> Añadir / Editar Galería
            </button>
        </div>
        <?php
    }

    /**
     * Guarda los datos del metabox.
     *
     * @param int $post_id ID del post.
     * @return void
     */
    public function save_meta_box_data( $post_id ) {
        if ( ! isset( $_POST['agp_nonce'] ) || ! wp_verify_nonce( $_POST['agp_nonce'], 'agp_save_action' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        // Guardamos tal cual viene del input oculto (separado por comas)
        if ( isset( $_POST['agp_image_ids'] ) ) {
            update_post_meta( $post_id, '_agp_image_ids', sanitize_text_field( $_POST['agp_image_ids'] ) );
        } else {
            // Si el campo está vacío, borramos el meta para limpiar la BD
            delete_post_meta( $post_id, '_agp_image_ids' );
        }
    }

    /**
     * Encola scripts y estilos de administración.
     *
     * @param string $hook Hook de la página actual.
     * @return void
     */
    public function enqueue_admin_assets( $hook ) {
        global $post;

        if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'agp_gallery' === $post->post_type ) {
            wp_enqueue_media();
            
            // Encolamos CSS de admin
            wp_enqueue_style(
                'agp-admin-css',
                AGP_PLUGIN_URL . 'assets/css/agp-admin.css',
                [],
                '1.1.0'
            );

            // Encolamos JS con dependencia de jquery-ui-sortable para el drag & drop
            wp_enqueue_script( 
                'agp-admin-js', 
                AGP_PLUGIN_URL . 'assets/js/agp-admin-uploader.js', 
                [ 'jquery', 'jquery-ui-sortable' ], 
                '1.1.0', 
                true 
            );
        }
    }
}