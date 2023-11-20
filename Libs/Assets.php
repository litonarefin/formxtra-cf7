<?php
namespace FORMXTRACF7\Libs;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Assets' ) ) {

	/**
	 * Assets Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 * @version     1.0.0
	 */
	class Assets {

		/**
		 * Constructor method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'formxtra_cf7_enqueue_scripts' ), 100 );
			add_action( 'admin_enqueue_scripts', array( $this, 'formxtra_cf7_admin_enqueue_scripts' ), 100 );
		}


		/**
		 * Get environment mode
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_mode() {
			return defined( 'WP_DEBUG' ) && WP_DEBUG ? 'development' : 'production';
		}

		/**
		 * Enqueue Scripts
		 *
		 * @method wp_enqueue_scripts()
		 */
		public function formxtra_cf7_enqueue_scripts() {

			// CSS Files .
			wp_enqueue_style( 'formxtra-cf7-frontend', FORMXTRACF7_ASSETS . 'css/formxtra-cf7-frontend.css', FORMXTRACF7_VER, 'all' );

			// JS Files .
			wp_enqueue_script( 'formxtra-cf7-frontend', FORMXTRACF7_ASSETS . 'js/formxtra-cf-7-frontend.js', array( 'jquery' ), FORMXTRACF7_VER, true );
		}


		/**
		 * Enqueue Scripts
		 *
		 * @method admin_enqueue_scripts()
		 */
		public function formxtra_cf7_admin_enqueue_scripts() {
			// CSS Files .
			wp_enqueue_style( 'formxtra-cf7-admin', FORMXTRACF7_ASSETS . 'css/formxtra-cf-7-admin.css', array( 'dashicons' ), FORMXTRACF7_VER, 'all' );

			// JS Files .
			wp_enqueue_script( 'formxtra-cf7-admin', FORMXTRACF7_ASSETS . 'js/formxtra-cf-7-admin.js', array( 'jquery' ), FORMXTRACF7_VER, true );
			wp_localize_script(
				'formxtra-cf7-admin',
				'FORMXTRACF7CORE',
				array(
					'admin_ajax'        => admin_url( 'admin-ajax.php' ),
					'recommended_nonce' => wp_create_nonce( 'formxtra_cf7_recommended_nonce' ),
					'is_premium'        => formxtra_cf7_license_client()->is_premium() ? true : false,
					'is_agency'         => formxtra_cf7_license_client()->is_plan( 'agency' ) ? true : false,
				)
			);
		}
	}
}