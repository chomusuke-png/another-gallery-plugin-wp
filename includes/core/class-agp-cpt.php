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
        ];

        $args = [
            'labels'       => $labels,
            'public'       => true,
            'has_archive'  => true,
            'menu_icon'    => 'dashicons-format-gallery',
            'supports'     => [ 'title', 'thumbnail' ], // Quitamos editor si solo usaremos metaboxes
            'show_in_rest' => true,
        ];

        register_post_type( 'agp_gallery', $args );
    }
}