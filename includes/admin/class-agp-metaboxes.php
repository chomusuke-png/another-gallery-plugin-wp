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
        
        $shortcode_view = sprintf( '[another_gallery_view id="%d"]', $post->ID );
        $shortcode_card = sprintf( '[another_gallery_card id="%d"]', $post->ID );
        ?>
        
        <div class="agp-info-box">
            <div class="agp-info-item">
                <strong><span class="dashicons dashicons-shortcode"></span> Shortcode para la Galería (Fotos):</strong>
                <input type="text" class="agp-code-input" value="<?php echo esc_attr( $shortcode_view ); ?>" readonly onclick="this.select();">
            </div>
            <div class="agp-info-item">
                <strong><span class="dashicons dashicons-id-alt"></span> Shortcode para la Tarjeta (Portada):</strong>
                <input type="text" class="agp-code-input" value="<?php echo esc_attr( $shortcode_card ); ?>" readonly onclick="this.select();">
            </div>
        </div>

        <div class="agp-uploader-wrap">
            <input type="hidden" id="agp_image_ids" name="agp_image_ids" value="<?php echo esc_attr( $ids ); ?>">
            
            <div id="agp-preview-container" class="<?php echo empty($id_array) ? 'agp-is-empty' : ''; ?>">
                <div class="agp-empty-placeholder">
                    <span class="dashicons dashicons-format-gallery"></span>
                    <p>Esta galería está vacía.</p>
                    <p class="small">Usa el botón de abajo para subir las fotos del interior.</p>
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
                    <span class="dashicons dashicons-cloud-upload"></span> Gestionar Fotos Interiores
                </button>
                <p class="description" style="margin-top:10px;">Arrastra las fotos para cambiar el orden.</p>
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
            
            // Estilos Admin UI (Shortcode Box)
            wp_enqueue_style( 'agp-admin-ui-css', AGP_PLUGIN_URL . 'assets/css/admin/agp-ui.css', [], '2.3.0' );
            
            // Estilos Admin Uploader (Dropzone)
            wp_enqueue_style( 'agp-admin-uploader-css', AGP_PLUGIN_URL . 'assets/css/admin/agp-uploader.css', [], '2.3.0' );
            
            // Script Uploader
            wp_enqueue_script( 'agp-admin-js', AGP_PLUGIN_URL . 'assets/js/agp-admin-uploader.js', [ 'jquery', 'jquery-ui-sortable' ], '2.3.0', true );
        }
    }
}