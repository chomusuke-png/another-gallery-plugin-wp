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
            'Gestión de Imágenes',
            [ $this, 'render_html' ],
            'agp_gallery',
            'normal',
            'high'
        );
    }

    /**
     * Renderiza el HTML del metabox.
     *
     * @param WP_Post $post
     * @return void
     */
    public function render_html( $post ) {
        wp_nonce_field( 'agp_save_action', 'agp_nonce' );
        $ids = get_post_meta( $post->ID, '_agp_image_ids', true );
        ?>
        <div class="agp-uploader-wrap">
            <input type="hidden" id="agp_image_ids" name="agp_image_ids" value="<?php echo esc_attr( $ids ); ?>">
            <div id="agp-preview-container" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:15px;">
                <?php if ( ! empty( $ids ) ) :
                    foreach ( explode( ',', $ids ) as $attachment_id ) :
                        $url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
                        if ( $url ) echo '<img src="' . esc_url( $url ) . '" style="width:80px;height:80px;object-fit:cover;">';
                    endforeach;
                endif; ?>
            </div>
            <button type="button" class="button button-secondary" id="agp-upload-btn">Añadir Imágenes</button>
        </div>
        <?php
    }

    /**
     * Guarda los datos.
     *
     * @param int $post_id
     * @return void
     */
    public function save_meta_box_data( $post_id ) {
        if ( ! isset( $_POST['agp_nonce'] ) || ! wp_verify_nonce( $_POST['agp_nonce'], 'agp_save_action' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if ( isset( $_POST['agp_image_ids'] ) ) {
            update_post_meta( $post_id, '_agp_image_ids', sanitize_text_field( $_POST['agp_image_ids'] ) );
        }
    }

    /**
     * Encola scripts de administración apuntando a assets/js.
     *
     * @param string $hook
     * @return void
     */
    public function enqueue_admin_assets( $hook ) {
        global $post;
        if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'agp_gallery' === $post->post_type ) {
            wp_enqueue_media();
            // Apuntamos a la carpeta assets/js
            wp_enqueue_script( 
                'agp-admin-js', 
                AGP_PLUGIN_URL . 'assets/js/agp-admin-uploader.js', 
                [ 'jquery' ], 
                '1.1.0', 
                true 
            );
        }
    }
}