<?php

/**
 * Widget para mostrar la Tarjeta (Card) de una galería en sidebars.
 */
class AGP_Widget extends WP_Widget {

    /**
     * Constructor del Widget.
     */
    public function __construct() {
        parent::__construct(
            'agp_gallery_card_widget',
            'AGP - Tarjeta de Galería',
            [ 'description' => 'Muestra la portada de una galería con efecto card.' ]
        );
    }

    /**
     * Renderiza el contenido del widget en el frontend.
     *
     * @param array $args     Argumentos del área de widget (before_widget, etc).
     * @param array $instance Configuración guardada del widget.
     * @return void
     */
    public function widget( $args, $instance ) {
        $gallery_id = ! empty( $instance['gallery_id'] ) ? $instance['gallery_id'] : 0;
        $custom_url = ! empty( $instance['custom_url'] ) ? $instance['custom_url'] : '';

        if ( ! $gallery_id ) return;

        echo $args['before_widget'];

        // Reutilizamos la lógica de renderizado existente en Shortcodes para no duplicar código HTML/CSS
        if ( class_exists( 'AGP_Shortcodes' ) ) {
            $shortcodes = new AGP_Shortcodes();
            echo $shortcodes->render_card([
                'id'  => $gallery_id,
                'url' => $custom_url
            ]);
        }

        echo $args['after_widget'];
    }

    /**
     * Renderiza el formulario de configuración en el Admin (Apariencia > Widgets).
     *
     * @param array $instance Configuración actual.
     * @return void
     */
    public function form( $instance ) {
        $gallery_id = ! empty( $instance['gallery_id'] ) ? $instance['gallery_id'] : '';
        $custom_url = ! empty( $instance['custom_url'] ) ? $instance['custom_url'] : '';

        // Obtenemos todas las galerías publicadas
        $galleries = get_posts([
            'post_type'      => 'agp_gallery',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC'
        ]);
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'gallery_id' ) ); ?>">Selecciona la Galería:</label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'gallery_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'gallery_id' ) ); ?>">
                <option value="">-- Seleccionar --</option>
                <?php foreach ( $galleries as $gallery ) : ?>
                    <option value="<?php echo esc_attr( $gallery->ID ); ?>" <?php selected( $gallery_id, $gallery->ID ); ?>>
                        <?php echo esc_html( $gallery->post_title ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'custom_url' ) ); ?>">URL Personalizada (Opcional):</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'custom_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'custom_url' ) ); ?>" type="text" value="<?php echo esc_attr( $custom_url ); ?>" placeholder="https://..." />
            <small>Si se deja vacío, enlazará automáticamente al post de la galería.</small>
        </p>
        <?php
    }

    /**
     * Guarda la configuración del widget.
     *
     * @param array $new_instance Nuevos valores.
     * @param array $old_instance Valores antiguos.
     * @return array Instancia sanitizada.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['gallery_id'] = ( ! empty( $new_instance['gallery_id'] ) ) ? intval( $new_instance['gallery_id'] ) : 0;
        $instance['custom_url'] = ( ! empty( $new_instance['custom_url'] ) ) ? sanitize_url( $new_instance['custom_url'] ) : '';
        return $instance;
    }
}