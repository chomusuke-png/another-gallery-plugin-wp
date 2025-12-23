<?php

class AGP_Shortcodes {

    /**
     * Encola scripts y estilos del frontend desde la carpeta assets.
     *
     * @return void
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style( 
            'agp-frontend-css', 
            AGP_PLUGIN_URL . 'assets/css/agp-style.css', 
            [], 
            '1.1.0' 
        );
        
        wp_enqueue_script( 
            'agp-frontend-js', 
            AGP_PLUGIN_URL . 'assets/js/agp-lightbox.js', 
            [], 
            '1.1.0', 
            true 
        );
    }

    /**
     * Renderiza la Card de portada.
     * [another_gallery_card id="123"]
     *
     * @param array $atts
     * @return string
     */
    public function render_card( $atts ) {
        $atts = shortcode_atts( [ 'id' => 0 ], $atts );
        $post_id = intval( $atts['id'] );

        if ( ! $post_id || get_post_type( $post_id ) !== 'agp_gallery' ) return '';

        $title = get_the_title( $post_id );
        $link  = get_permalink( $post_id );
        $thumb = get_the_post_thumbnail_url( $post_id, 'medium_large' ); // Mejor calidad para card

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
     * [another_gallery_view]
     *
     * @param array $atts
     * @return string
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
                $thumb = wp_get_attachment_image_url( $img_id, 'medium' );
                $full  = wp_get_attachment_image_url( $img_id, 'large' ); // Full para lightbox
            ?>
                <div class="agp-grid-item">
                    <img src="<?php echo esc_url( $thumb ); ?>" 
                         data-full="<?php echo esc_url( $full ); ?>" 
                         class="agp-lightbox-trigger" 
                         alt="Gallery">
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