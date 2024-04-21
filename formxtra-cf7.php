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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Remove <p> and <br/> from Contact Form 7
add_filter('wpcf7_autop_or_not', '__return_false');

// Contact Form 7 - Stop loading the JavaScript and CSS stylesheet on all pages
add_filter('wpcf7_load_js', '__return_false');
add_filter('wpcf7_load_css', '__return_false');
