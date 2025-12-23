<?php

require_once AGP_PLUGIN_PATH . 'includes/core/class-agp-cpt.php';
require_once AGP_PLUGIN_PATH . 'includes/admin/class-agp-metaboxes.php';
require_once AGP_PLUGIN_PATH . 'includes/frontend/class-agp-shortcodes.php';

class AGP_Loader {

    /**
     * Registra todos los hooks del plugin.
     *
     * @return void
     */
    public function run() {
        // Core: CPT
        $cpt = new AGP_CPT();
        add_action( 'init', [ $cpt, 'register_post_type' ] );
        
        // Core: Columnas de Admin
        add_filter( 'manage_agp_gallery_posts_columns', [ $cpt, 'add_admin_columns' ] );
        add_action( 'manage_agp_gallery_posts_custom_column', [ $cpt, 'render_admin_columns' ], 10, 2 );

        // Admin: Metaboxes
        $metaboxes = new AGP_Metaboxes();
        add_action( 'add_meta_boxes', [ $metaboxes, 'add_meta_box' ] );
        add_action( 'save_post', [ $metaboxes, 'save_meta_box_data' ] );
        add_action( 'admin_enqueue_scripts', [ $metaboxes, 'enqueue_admin_assets' ] );

        // Frontend: Shortcodes & Assets
        $shortcodes = new AGP_Shortcodes();
        add_shortcode( 'another_gallery_card', [ $shortcodes, 'render_card' ] );
        add_shortcode( 'another_gallery_view', [ $shortcodes, 'render_gallery' ] );
        add_action( 'wp_enqueue_scripts', [ $shortcodes, 'enqueue_frontend_assets' ] );
    }
}