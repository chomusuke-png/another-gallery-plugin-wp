<?php
/**
 * Plugin Name: Another Gallery Plugin
 * Description: Plugin modular de galerías con estructura assets/includes.
 * Version: 1.1
 * Author: Zumito
 * Text Domain: another-gallery-plugin
 */

defined( 'ABSPATH' ) || exit;

// Definimos la ruta base para facilitar los includes
define( 'AGP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'AGP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once AGP_PLUGIN_PATH . 'includes/core/class-agp-loader.php';

/**
 * Inicializa el plugin.
 *
 * @return void
 */
function agp_init_plugin() {
    $loader = new AGP_Loader();
    $loader->run();
}

add_action( 'plugins_loaded', 'agp_init_plugin' );