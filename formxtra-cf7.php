<?php
/**
 * Plugin Name: Formxtra CF7
 * Plugin URI:  https://formxtra.com/contact-form-7
 * Description: Formxtra CF7 - Contact Form 7 Addons WordPress Plugin
 * Version:     1.0.0
 * Author:      Jewel Theme
 * Author URI:  https://jeweltheme.com
 * Text Domain: formxtra-cf7
 * Domain Path: languages/
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package formxtra-cf7
 */

/*
 * don't call the file directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	wp_die( esc_html__( 'You can\'t access this page', 'formxtra-cf7' ) );
}

$formxtra_cf7_plugin_data = get_file_data(
	__FILE__,
	array(
		'Version'     => 'Version',
		'Plugin Name' => 'Plugin Name',
		'Author'      => 'Author',
		'Description' => 'Description',
		'Plugin URI'  => 'Plugin URI',
	),
	false
);

// Define Constants.
if ( ! defined( 'FORMXTRACF7' ) ) {
	define( 'FORMXTRACF7', $formxtra_cf7_plugin_data['Plugin Name'] );
}

if ( ! defined( 'FORMXTRACF7_VER' ) ) {
	define( 'FORMXTRACF7_VER', $formxtra_cf7_plugin_data['Version'] );
}

if ( ! defined( 'FORMXTRACF7_AUTHOR' ) ) {
	define( 'FORMXTRACF7_AUTHOR', $formxtra_cf7_plugin_data['Author'] );
}

if ( ! defined( 'FORMXTRACF7_DESC' ) ) {
	define( 'FORMXTRACF7_DESC', $formxtra_cf7_plugin_data['Author'] );
}

if ( ! defined( 'FORMXTRACF7_URI' ) ) {
	define( 'FORMXTRACF7_URI', $formxtra_cf7_plugin_data['Plugin URI'] );
}

if ( ! defined( 'FORMXTRACF7_DIR' ) ) {
	define( 'FORMXTRACF7_DIR', __DIR__ );
}

if ( ! defined( 'FORMXTRACF7_FILE' ) ) {
	define( 'FORMXTRACF7_FILE', __FILE__ );
}

if ( ! defined( 'FORMXTRACF7_SLUG' ) ) {
	define( 'FORMXTRACF7_SLUG', dirname( plugin_basename( __FILE__ ) ) );
}

if ( ! defined( 'FORMXTRACF7_BASE' ) ) {
	define( 'FORMXTRACF7_BASE', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'FORMXTRACF7_PATH' ) ) {
	define( 'FORMXTRACF7_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'FORMXTRACF7_URL' ) ) {
	define( 'FORMXTRACF7_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
}

if ( ! defined( 'FORMXTRACF7_INC' ) ) {
	define( 'FORMXTRACF7_INC', FORMXTRACF7_PATH . '/Inc/' );
}

if ( ! defined( 'FORMXTRACF7_LIBS' ) ) {
	define( 'FORMXTRACF7_LIBS', FORMXTRACF7_PATH . 'Libs' );
}

if ( ! defined( 'FORMXTRACF7_ASSETS' ) ) {
	define( 'FORMXTRACF7_ASSETS', FORMXTRACF7_URL . 'assets/' );
}

if ( ! defined( 'FORMXTRACF7_IMAGES' ) ) {
	define( 'FORMXTRACF7_IMAGES', FORMXTRACF7_ASSETS . 'images/' );
}

if ( ! class_exists( '\\FORMXTRACF7\\Formxtra_CF7' ) ) {
	// Autoload Files.
	include_once FORMXTRACF7_DIR . '/vendor/autoload.php';
	// Instantiate Formxtra_CF7 Class.
	include_once FORMXTRACF7_DIR . '/class-formxtra-cf7.php';
}

// Activation and Deactivation hooks.
if ( class_exists( '\\FORMXTRACF7\\Formxtra_CF7' ) ) {
	register_activation_hook( FORMXTRACF7_FILE, array( '\\FORMXTRACF7\\Formxtra_CF7', 'formxtra_cf7_activation_hook' ) );
	// register_deactivation_hook( FORMXTRACF7_FILE, array( '\\FORMXTRACF7\\Formxtra_CF7', 'formxtra_cf7_deactivation_hook' ) );
}
