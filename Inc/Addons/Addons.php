<?php

namespace FORMXTRACF7\Inc\Addons;

use FORMXTRACF7\Inc\Addons\Redirect\Redirect;
use FORMXTRACF7\Inc\Addons\Signature\Signature;
use FORMXTRACF7\Inc\Addons\Database\Database;

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Addons
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Addons
{


	/**
	 * Construct Method
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function __construct()
	{
		Redirect::get_instance();
		Signature::get_instance();
		Database::get_instance();
		// new Signature();
	}
}
