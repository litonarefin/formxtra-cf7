<?php
namespace FORMXTRACF7\Inc\Addons;

use FORMXTRACF7\Inc\Addons\Redirect\Redirect;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Addons
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Addons {


	/**
	 * Construct Method
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
    public function __construct(){
		Redirect::get_instance();
    }
}
