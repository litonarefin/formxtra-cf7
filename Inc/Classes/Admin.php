<?php

namespace FORMXTRACF7\Inc\Classes;

use FORMXTRACF7\Inc\Addons\Addons;


// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Admin
{


    /**
     * Construct Method
     *
     * @return void
     * @author Jewel Theme <support@jeweltheme.com>
     */
    public function __construct()
    {
        $this->include_addons();
        add_action('admin_menu', array($this, 'formxtra_cf7_admin_menu'));
    }

    public function include_addons()
    {
        new Addons();
    }

    /**
     * Admin Menu
     */
    public function formxtra_cf7_admin_menu()
    {
        add_submenu_page(
            'wpcf7', //parent slug
            __('Formxtra CF7', 'formxtra-cf7'), // page_title
            __('Formxtra CF7', 'formxtra-cf7'), // menu_title
            'manage_options', // capability
            'formxtra-cf7-settings', // menu_slug
            array($this, 'formxtra_cf7_settings_page') // callback function
        );
    }

    /**
     * Settings Page
     */
    public function formxtra_cf7_settings_page()
    {
        echo '<h3>This is Settings Page</h3>';
    }
}
