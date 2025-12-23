<?php

class AGP_CPT {

    /**
     * Registra el tipo de post 'agp_gallery'.
     *
     * @return void
     */
    public function register_post_type() {
        $labels = [
            'name'          => 'Galerías',
            'singular_name' => 'Galería',
            'add_new_item'  => 'Añadir Nueva Galería',
            'edit_item'     => 'Editar Galería',
            'search_items'  => 'Buscar Galerías',
        ];

        $args = [
            'labels'       => $labels,
            'public'       => true,
            'has_archive'  => true,
            'menu_icon'    => 'dashicons-format-gallery',
            'supports'     => [ 'title', 'thumbnail' ],
            'show_in_rest' => true,
        ];

        register_post_type( 'agp_gallery', $args );
    }

    /**
     * Define las columnas personalizadas para el listado de galerías.
     *
     * @param array $columns Columnas existentes.
     * @return array Columnas modificadas.
     */
    public function add_admin_columns( $columns ) {
        // Insertamos la columna Shortcode después del Título (fecha suele ir al final)
        $new_columns = [];
        foreach ( $columns as $key => $title ) {
            $new_columns[ $key ] = $title;
            if ( 'title' === $key ) {
                $new_columns['agp_shortcode'] = 'Shortcode';
            }
        }
        return $new_columns;
    }

    /**
     * Renderiza el contenido de las columnas personalizadas.
     *
     * @param string $column Nombre de la columna.
     * @param int    $post_id ID del post actual.
     * @return void
     */
    public function render_admin_columns( $column, $post_id ) {
        if ( 'agp_shortcode' === $column ) {
            // Creamos un input readonly para facilitar la copia con un clic
            $shortcode = sprintf( '[another_gallery_view id="%d"]', $post_id );
            echo sprintf(
                '<input type="text" value="%s" readonly style="width:100%%; background:#f9f9f9; border:1px solid #ddd; padding:5px; box-shadow:none;" onclick="this.select();">',
                esc_attr( $shortcode )
            );
        }
    }
}