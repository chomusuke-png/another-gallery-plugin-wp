<?php

class AGP_Shortcodes {

    public function enqueue_frontend_assets() {
        // Encolamos los estilos modulares
        wp_enqueue_style( 'agp-card-css', AGP_PLUGIN_URL . 'assets/css/frontend/agp-card.css', [], '2.3.0' );
        wp_enqueue_style( 'agp-grid-css', AGP_PLUGIN_URL . 'assets/css/frontend/agp-grid.css', [], '2.3.0' );
        wp_enqueue_style( 'agp-lightbox-css', AGP_PLUGIN_URL . 'assets/css/frontend/agp-lightbox.css', [], '2.3.0' );
        
        // JS del Lightbox
        wp_enqueue_script( 'agp-frontend-js', AGP_PLUGIN_URL . 'assets/js/agp-lightbox.js', [], '2.3.0', true );
    }

    /**
     * Renderiza la Card de portada con enlace opcional.
     * Uso: [another_gallery_card id="123" url="https://misitio.com/mi-pagina"]
     */
    public function render_card( $atts ) {
        $atts = shortcode_atts( [ 
            'id'  => 0,
            'url' => ''
        ], $atts );
        
        $post_id = intval( $atts['id'] );

        if ( ! $post_id || get_post_type( $post_id ) !== 'agp_gallery' ) return '';

        $title = get_the_title( $post_id );
        $link = ! empty( $atts['url'] ) ? $atts['url'] : get_permalink( $post_id );
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
     * Renderiza la galería con descripciones.
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
                $thumb = wp_get_attachment_image_url( $img_id, 'medium_large' );
                $full  = wp_get_attachment_image_url( $img_id, 'full' );
                $attachment = get_post( $img_id );
                $caption = $attachment->post_excerpt; 
            ?>
                <div class="agp-grid-item">
                    <img src="<?php echo esc_url( $thumb ); ?>" 
                         data-full="<?php echo esc_url( $full ); ?>" 
                         data-desc="<?php echo esc_attr( $caption ); ?>"
                         class="agp-lightbox-trigger" 
                         alt="<?php echo esc_attr( $caption ); ?>">
                </div>
            <?php endforeach; ?>
        </div>
        
        <div id="agp-modal" class="agp-modal">
            <span class="agp-close">&times;</span>
            <div class="agp-modal-wrapper">
                <img class="agp-modal-content" id="agp-modal-img">
                <div id="agp-caption-text"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}