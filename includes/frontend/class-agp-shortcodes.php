<?php

class AGP_Shortcodes {

    /**
     * Encola scripts y estilos del frontend.
     */
    public function enqueue_frontend_assets() {
        // Asegúrate de que la versión coincida para forzar la recarga del cache si cambias el CSS
        wp_enqueue_style( 'agp-frontend-css', AGP_PLUGIN_URL . 'assets/css/agp-style.css', [], '1.2.0' );
        wp_enqueue_script( 'agp-frontend-js', AGP_PLUGIN_URL . 'assets/js/agp-lightbox.js', [], '1.2.0', true );
    }

    /**
     * Renderiza la Card de portada.
     * Shortcode: [another_gallery_card id="123"]
     */
    public function render_card( $atts ) {
        $atts = shortcode_atts( [ 'id' => 0 ], $atts );
        $post_id = intval( $atts['id'] );

        if ( ! $post_id || get_post_type( $post_id ) !== 'agp_gallery' ) return '';

        $title = get_the_title( $post_id );
        $link  = get_permalink( $post_id );
        $thumb = get_the_post_thumbnail_url( $post_id, 'medium_large' );

        ob_start();
        ?>
        <div class="agp-card">
            <div class="agp-card-thumb" style="background-image: url('<?php echo esc_url( $thumb ); ?>');"></div>
            
            <div class="agp-card-body">
                <h3 class="agp-card-title"><?php echo esc_html( $title ); ?></h3>
                <a href="<?php echo esc_url( $link ); ?>" class="agp-btn">Ver Fotos</a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Renderiza la galería completa.
     * Shortcode: [another_gallery_view]
     */
    public function render_gallery( $atts ) {
        $post_id = isset( $atts['id'] ) ? intval( $atts['id'] ) : get_the_ID();
        $ids = get_post_meta( $post_id, '_agp_image_ids', true );

        if ( empty( $ids ) ) return '';

        $id_array = explode( ',', $ids );

        ob_start();
        ?>
        <div class="agp-grid-container">
            <?php foreach ( $id_array as $img_id ) : 
                $thumb = wp_get_attachment_image_url( $img_id, 'medium_large' ); // Mejor calidad para masonry
                $full  = wp_get_attachment_image_url( $img_id, 'full' ); // Full real para lightbox
            ?>
                <div class="agp-grid-item">
                    <img src="<?php echo esc_url( $thumb ); ?>" 
                         data-full="<?php echo esc_url( $full ); ?>" 
                         class="agp-lightbox-trigger" 
                         alt="Gallery Image">
                </div>
            <?php endforeach; ?>
        </div>
        
        <div id="agp-modal" class="agp-modal">
            <span class="agp-close">&times;</span>
            <img class="agp-modal-content" id="agp-modal-img">
        </div>
        <?php
        return ob_get_clean();
    }
}