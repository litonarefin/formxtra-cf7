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

        if (defined('WPCF7_VERSION') && WPCF7_VERSION >= 5.7) {
            add_filter('wpcf7_autop_or_not', '__return_false');
        }
    }

    public function include_addons()
    {
        if (class_exists('WPCF7')) {
            new Addons();
        } else {
            //Admin notice
            add_action('admin_notices', array($this, 'formxtra_cf7_admin_notice'));
        }
    }


    /*
    * Admin notice- To check the Contact form 7 plugin is installed
    */
    public function formxtra_cf7_admin_notice()
    { ?>
        <div class="formxtra-cf7 notice notice-error">
            <p>
                <?php printf(
                    __('%s requires %s to be installed and active. You can install and activate it from %s', 'formxtra-cf7'),
                    '<strong>Formxtra CF7 for Contact Form 7</strong>',
                    '<strong>Contact form 7</strong>',
                    '<a href="' . admin_url('plugin-install.php?tab=search&s=contact+form+7') . '">here</a>.'
                ); ?></p>
        </div>
<?php
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
