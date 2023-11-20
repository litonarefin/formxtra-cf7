<?php
namespace FORMXTRACF7\Inc\Addons;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Addons
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Redirect {

    private static $instance = null;

	/**
	 * Construct Method
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
    public function __construct(){
        // $this->redirect_init();
        add_action( 'wpcf7_editor_panels', array( $this, 'formxtra_cf7_redirect_add_panel' ) );
		add_action( 'wpcf7_after_save', array( $this, 'formxtra_cf7_redirect_save_meta' ) );
		add_action( 'wpcf7_submit', array( $this, 'formxtra_cf7_redirect_non_ajax_redirection' ) );
    }

    /**
     * Add Tab Panel
     *
     * @return void
     * @author Jewel Theme <support@jeweltheme.com>
     */
    public function formxtra_cf7_redirect_add_panel( $panels ){
		$panels['formxtra-cf7-redirect-panel'] = array(
			'title'    => __( 'FXCF7 Redirection', 'formxtra-cf7' ),
			'callback' => array( $this, 'formxtra_cf7_create_redirect_panel_fields' ),
		);
		return $panels;
    }

    /*
    * Redirect Fields Function
    */
    public function formxtra_cf7_create_redirect_panel_fields( $post ){
        return 'Panel Fields Content';
    }


    /**
     * Add Panel
     *
     * @return void
     * @author Jewel Theme <support@jeweltheme.com>
     */
    public function redirect_init(){

    }



    /**
     * Returns the singleton instance of the class.
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Redirect ) ) {
            self::$instance = new Redirect();
            // self::$instance->redirect_init();
        }

        return self::$instance;
    }
}
