<?php

namespace FORMXTRACF7\Inc\Classes\Notifications;

use FORMXTRACF7\Inc\Classes\Notifications\Model\Notice;

if (!class_exists('Post_Publishing')) {
	/**
	 * Ask For Rating Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 */
	class Post_Publishing extends Notice
	{
		public function __construct()
		{
			add_action('wpcf7_admin_misc_pub_section', array($this, ''));
		}
	}
}
