<?php

if ( ! function_exists( 'formxtra_cf7_license_client' ) ) {
	/**
	 * License Client function
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	function formxtra_cf7_license_client() {
		global $formxtra_cf7_license_client;

		if ( ! isset( $formxtra_cf7_license_client ) ) {
			// Include SDK.
			require_once FORMXTRACF7_LIBS . '/License/Loader.php';

			$formxtra_cf7_license_client = new \FORMXTRACF7\Libs\License\Loader(
				array(
					'plugin_root'      => FORMXTRACF7_FILE,
					'software_version' => FORMXTRACF7_VER,
					'software_title'   => 'Formxtra CF7',
					'product_id'       => '',
					'redirect_url'     => admin_url( 'admin.php?page=' . FORMXTRACF7_SLUG . '-license-activation' ),
					'software_type'    => 'plugin', // theme/plugin .
					'api_end_point'    => \FORMXTRACF7\Libs\Helper::api_endpoint(),
					'text_domain'      => 'formxtra-cf7',
					'license_menu'     => array(
						'icon_url'    => 'dashicons-image-filter',
						'position'    => 40,
						'menu_type'   => 'add_submenu_page', // 'add_submenu_page',
                        'parent_slug' => '-settings',
						'menu_title'  => __( 'License', 'formxtra-cf7' ),
						'page_title'  => __( 'License Activation', 'formxtra-cf7' ),
					),
				)
			);
		}

		return $formxtra_cf7_license_client;
	}

	// Init Formxtra_CF7_Wc_Client.
	formxtra_cf7_license_client();

	// Signal that Formxtra_CF7_Wc_Client was initiated.
	do_action( 'formxtra_cf7_license_client_loaded' );
}