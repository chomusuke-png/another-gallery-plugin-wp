<?php

class AGP_Metaboxes {

    public function add_meta_box() {
        add_meta_box(
            'agp_gallery_images',
            'Gestión de la Galería',
            [ $this, 'render_html' ],
            'agp_gallery',
            'normal',
            'high'
        );
    }

    public function render_html( $post ) {
        wp_nonce_field( 'agp_save_action', 'agp_nonce' );
        $ids = get_post_meta( $post->ID, '_agp_image_ids', true );
        $id_array = ! empty( $ids ) ? explode( ',', $ids ) : [];
        
        // Generamos los shortcodes dinámicamente para mostrarlos
        $shortcode_view = sprintf( '[another_gallery_view id="%d"]', $post->ID );
        $shortcode_card = sprintf( '[another_gallery_card id="%d"]', $post->ID );
        ?>
        
        <div class="agp-info-box">
            <div class="agp-info-item">
                <strong><span class="dashicons dashicons-images-alt2"></span> Para mostrar la galería completa:</strong>
                <input type="text" class="agp-code-input" value="<?php echo esc_attr( $shortcode_view ); ?>" readonly onclick="this.select();">
                <p class="description">Copia y pega este código en cualquier página o entrada.</p>
            </div>
            <div class="agp-info-item">
                <strong><span class="dashicons dashicons-cover-image"></span> Para mostrar la tarjeta de portada:</strong>
                <input type="text" class="agp-code-input" value="<?php echo esc_attr( $shortcode_card ); ?>" readonly onclick="this.select();">
                <p class="description">Ideal para índices o página de inicio. (Recuerda poner una URL opcional si quieres redirigir).</p>
            </div>
        </div>

        <hr class="agp-divider">

        <div class="agp-uploader-wrap">
            <input type="hidden" id="agp_image_ids" name="agp_image_ids" value="<?php echo esc_attr( $ids ); ?>">
            
            <div id="agp-preview-container" class="<?php echo empty($id_array) ? 'agp-is-empty' : ''; ?>">
                <div class="agp-empty-placeholder">
                    <span class="dashicons dashicons-format-gallery"></span>
                    <p>Esta galería está vacía.</p>
                    <p class="small">Haz clic en el botón de abajo para añadir tus fotos.</p>
                </div>

                <?php foreach ( $id_array as $attachment_id ) : 
                    $url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
                    if ( $url ) : ?>
                        <div class="agp-img-wrapper" data-id="<?php echo esc_attr( $attachment_id ); ?>">
                            <img src="<?php echo esc_url( $url ); ?>" alt="Gallery Image">
                            <div class="agp-img-actions">
                                <span class="agp-remove-img" title="Eliminar">&times;</span>
                            </div>
                        </div>
                    <?php endif; 
                endforeach; ?>
            </div>

            <div class="agp-actions-bar">
                <button type="button" class="button button-primary button-hero" id="agp-upload-btn">
                    <span class="dashicons dashicons-cloud-upload"></span> Añadir / Gestionar Imágenes
                </button>
                <p class="description" style="margin-top:10px;">💡 Tip: Puedes arrastrar y soltar las imágenes para cambiar el orden.</p>
            </div>
        </div>
        <?php
    }

    public function save_meta_box_data( $post_id ) {
        if ( ! isset( $_POST['agp_nonce'] ) || ! wp_verify_nonce( $_POST['agp_nonce'], 'agp_save_action' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if ( isset( $_POST['agp_image_ids'] ) ) {
            update_post_meta( $post_id, '_agp_image_ids', sanitize_text_field( $_POST['agp_image_ids'] ) );
        } else {
            delete_post_meta( $post_id, '_agp_image_ids' );
        }
    }

    public function enqueue_admin_assets( $hook ) {
        global $post;
        if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'agp_gallery' === $post->post_type ) {
            wp_enqueue_media();
            // Actualizamos versión para limpiar caché
            wp_enqueue_style( 'agp-admin-css', AGP_PLUGIN_URL . 'assets/css/agp-admin.css', [], '2.0.0' );
            wp_enqueue_script( 'agp-admin-js', AGP_PLUGIN_URL . 'assets/js/agp-admin-uploader.js', [ 'jquery', 'jquery-ui-sortable' ], '2.0.0', true );
        }
    }
}