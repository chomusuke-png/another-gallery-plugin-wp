<?php

class AGP_Shortcodes {

    public function enqueue_frontend_assets() {
        wp_enqueue_style( 'agp-frontend-css', AGP_PLUGIN_URL . 'assets/css/agp-style.css', [], '1.1.0' );
        wp_enqueue_script( 'agp-frontend-js', AGP_PLUGIN_URL . 'assets/js/agp-lightbox.js', [], '1.1.0', true );
    }

    public function render_card( $atts ) {
        $atts = shortcode_atts( [ 'id' => 0 ], $atts );
        $post_id = intval( $atts['id'] );

        if ( ! $post_id || get_post_type( $post_id ) !== 'agp_gallery' ) return '';

        $title = get_the_title( $post_id );
        $link  = get_permalink( $post_id );
        // Nota: Asegúrate de usar 'agp-card-image' si tu CSS usa esa clase, o 'agp-card-thumb' si cambiaste el CSS.
        // Basado en tu CSS provisto (agp-style.css), la clase correcta es 'agp-card-image'.
        $thumb = get_the_post_thumbnail_url( $post_id, 'medium_large' ); 

        ob_start();
        ?>
        <div class="agp-card">
            <div class="agp-card-image" style="background-image: url('<?php echo esc_url( $thumb ); ?>');"></div>
            <div class="agp-card-content">
                <h3><?php echo esc_html( $title ); ?></h3>
                <a href="<?php echo esc_url( $link ); ?>" class="agp-button">Ver Fotos</a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_gallery( $atts ) {
        $post_id = isset( $atts['id'] ) ? intval( $atts['id'] ) : get_the_ID();
        $ids = get_post_meta( $post_id, '_agp_image_ids', true );

        if ( empty( $ids ) ) return '';

        $id_array = explode( ',', $ids );

        ob_start();
        ?>
        <div class="agp-gallery-grid">
            <?php foreach ( $id_array as $img_id ) : 
                $thumb = wp_get_attachment_image_url( $img_id, 'medium' );
                $full  = wp_get_attachment_image_url( $img_id, 'large' );
            ?>
                <div class="agp-gallery-item">
                    <img src="<?php echo esc_url( $thumb ); ?>" 
                         data-full="<?php echo esc_url( $full ); ?>" 
                         class="agp-lightbox-trigger" 
                         alt="Gallery">
                </div>
            <?php endforeach; ?>
        </div>
        
        <div id="agp-lightbox" class="agp-lightbox">
            <span class="agp-close">&times;</span>
            <img class="agp-lightbox-content" id="agp-img-full">
        </div>
        <?php
        return ob_get_clean();
    }
}