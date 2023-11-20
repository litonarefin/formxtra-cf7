<?php
namespace FORMXTRACF7;

use FORMXTRACF7\Libs\Assets;
use FORMXTRACF7\Libs\Helper;
use FORMXTRACF7\Libs\Featured;
use FORMXTRACF7\Inc\Classes\Recommended_Plugins;
use FORMXTRACF7\Inc\Classes\Notifications\Notifications;
use FORMXTRACF7\Inc\Classes\Pro_Upgrade;
use FORMXTRACF7\Inc\Classes\Upgrade_Plugin;
use FORMXTRACF7\Inc\Classes\Feedback;
use FORMXTRACF7\Inc\Classes\Addons;

/**
 * Main Class
 *
 * @Formxtra CF7
 * Jewel Theme <support@jeweltheme.com>
 * @version     1.0.0
 */

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formxtra_CF7 Class
 */
if ( ! class_exists( '\FORMXTRACF7\Formxtra_CF7' ) ) {

	/**
	 * Class: Formxtra_CF7
	 */
	final class Formxtra_CF7 {

		const VERSION            = FORMXTRACF7_VER;
		private static $instance = null;

		/**
		 * what we collect construct method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct() {
			$this->includes();
			add_action( 'plugins_loaded', array( $this, 'formxtra_cf7_plugins_loaded' ), 999 );
			// Body Class.
			add_filter( 'admin_body_class', array( $this, 'formxtra_cf7_body_class' ) );
			// This should run earlier .
			// add_action( 'plugins_loaded', [ $this, 'formxtra_cf7_maybe_run_upgrades' ], -100 ); .
		}

		/**
		 * plugins_loaded method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function formxtra_cf7_plugins_loaded() {
			$this->formxtra_cf7_activate();
		}

		/**
		 * Version Key
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function plugin_version_key() {
			return Helper::formxtra_cf7_slug_cleanup() . '_version';
		}

		/**
		 * Activation Hook
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function formxtra_cf7_activate() {
			$current_formxtra_cf7_version = get_option( self::plugin_version_key(), null );

			if ( get_option( 'formxtra_cf7_activation_time' ) === false ) {
				update_option( 'formxtra_cf7_activation_time', strtotime( 'now' ) );
			}

			if ( is_null( $current_formxtra_cf7_version ) ) {
				update_option( self::plugin_version_key(), self::VERSION );
			}

			$allowed = get_option( Helper::formxtra_cf7_slug_cleanup() . '_allow_tracking', 'no' );

			// if it wasn't allowed before, do nothing .
			if ( 'yes' !== $allowed ) {
				return;
			}
			// re-schedule and delete the last sent time so we could force send again .
			$hook_name = Helper::formxtra_cf7_slug_cleanup() . '_tracker_send_event';
			if ( ! wp_next_scheduled( $hook_name ) ) {
				wp_schedule_event( time(), 'weekly', $hook_name );
			}
		}


		/**
		 * Add Body Class
		 *
		 * @param [type] $classes .
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function formxtra_cf7_body_class( $classes ) {
			$classes .= ' formxtra-cf7 ';
			return $classes;
		}

		/**
		 * Run Upgrader Class
		 *
		 * @return void
		 */
		public function formxtra_cf7_maybe_run_upgrades() {
			if ( ! is_admin() && ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Run Upgrader .
			$upgrade = new Upgrade_Plugin();

			// Need to work on Upgrade Class .
			if ( $upgrade->if_updates_available() ) {
				$upgrade->run_updates();
			}
		}

		/**
		 * Include methods
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function includes() {
			new Assets();
			new Recommended_Plugins();
			new Pro_Upgrade();
			new Notifications();
			new Featured();
			new Feedback();
			new Addons();
		}


		/**
		 * Initialization
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function formxtra_cf7_init() {
			$this->formxtra_cf7_load_textdomain();
		}


		/**
		 * Text Domain
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function formxtra_cf7_load_textdomain() {
			$domain = 'formxtra-cf7';
			$locale = apply_filters( 'formxtra_cf7_plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, false, dirname( FORMXTRACF7_BASE ) . '/languages/' );
		}

		/**
		* Deactivate Pro Plugin if it's not already active
		*
		* @author Jewel Theme <support@jeweltheme.com>
		*/
		public static function formxtra_cf7_activation_hook() {
			if ( formxtra_cf7_license_client()->is_free_plan() ) {
				$plugin = 'formxtra-cf7-pro/formxtra-cf7.php';
			} else {
				$plugin = 'formxtra-cf7/formxtra-cf7.php';
			}
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
			}
		}


		/**
		 * Returns the singleton instance of the class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Formxtra_CF7 ) ) {
				self::$instance = new Formxtra_CF7();
				self::$instance->formxtra_cf7_init();
			}

			return self::$instance;
		}
	}

	// Get Instant of Formxtra_CF7 Class .
	Formxtra_CF7::get_instance();
}
