<?php
namespace FORMXTRACF7\Inc\Classes;

use FORMXTRACF7\Inc\Classes\Notifications\Base\User_Data;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Feedback
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Feedback {

    use User_Data;

	/**
	 * Construct Method
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
    public function __construct(){
        add_action( 'admin_enqueue_scripts' , [ $this,'admin_suvery_scripts'] );
        add_action( 'admin_footer' , [ $this , 'deactivation_footer' ] );
        add_action( 'wp_ajax_formxtra_cf7_deactivation_survey', array( $this, 'formxtra_cf7_deactivation_survey' ) );

    }


    public function proceed(){

        global $current_screen;
        if(
            isset($current_screen->parent_file)
            && $current_screen->parent_file == 'plugins.php'
            && isset($current_screen->id)
            && $current_screen->id == 'plugins'
        ){
           return true;
        }
        return false;

    }

    public function admin_suvery_scripts($handle){
        if('plugins.php' === $handle){
            wp_enqueue_style( 'formxtra_cf7-survey' , FORMXTRACF7_ASSETS . 'css/formxtra-cf7-survey.css' );
        }
    }

    /**
     * Deactivation Survey
     */
    public function formxtra_cf7_deactivation_survey(){
        check_ajax_referer( 'formxtra_cf7_deactivation_nonce' );

        $deactivation_reason  = ! empty( $_POST['deactivation_reason'] ) ? sanitize_text_field( wp_unslash( $_POST['deactivation_reason'] ) ) : '';

        if( empty( $deactivation_reason )){
            return;
        }

        $email = get_bloginfo( 'admin_email' );
        $author_obj = get_user_by( 'email', $email );
        $user_id    = $author_obj->ID;
        $full_name  = $author_obj->display_name;

        $response = $this->get_collect_data( $user_id, array(
            'first_name'              => $full_name,
            'email'                   => $email,
            'deactivation_reason'     => $deactivation_reason,
        ) );

        return $response;
    }


    public function get_survey_questions(){

        return [
			'no_longer_needed' => [
				'title' => esc_html__( 'I no longer need the plugin', 'formxtra-cf7' ),
				'input_placeholder' => '',
			],
			'found_a_better_plugin' => [
				'title' => esc_html__( 'I found a better plugin', 'formxtra-cf7' ),
				'input_placeholder' => esc_html__( 'Please share which plugin', 'formxtra-cf7' ),
			],
			'couldnt_get_the_plugin_to_work' => [
				'title' => esc_html__( 'I couldn\'t get the plugin to work', 'formxtra-cf7' ),
				'input_placeholder' => '',
			],
			'temporary_deactivation' => [
				'title' => esc_html__( 'It\'s a temporary deactivation', 'formxtra-cf7' ),
				'input_placeholder' => '',
			],
			'formxtra_cf7_pro' => [
				'title' => sprintf( esc_html__( 'I have %1$s Pro', 'formxtra-cf7' ), FORMXTRACF7 ),
				'input_placeholder' => '',
				'alert' => sprintf( esc_html__( 'Wait! Don\'t deactivate %1$s. You have to activate both %1$s and %1$s Pro in order for the plugin to work.', 'formxtra-cf7' ), FORMXTRACF7 ),
			],
			'need_better_design' => [
				'title' => esc_html__( 'I need better design and presets', 'formxtra-cf7' ),
				'input_placeholder' => esc_html__( 'Let us know your thoughts', 'formxtra-cf7' ),
			],
            'other' => [
				'title' => esc_html__( 'Other', 'formxtra-cf7' ),
				'input_placeholder' => esc_html__( 'Please share the reason', 'formxtra-cf7' ),
			],
		];
    }


        /**
         * Deactivation Footer
         */
        public function deactivation_footer(){

        if(!$this->proceed()){
            return;
        }

        ?>
        <div class="formxtra-cf7-deactivate-survey-overlay" id="formxtra-cf7-deactivate-survey-overlay"></div>
        <div class="formxtra-cf7-deactivate-survey-modal" id="formxtra-cf7-deactivate-survey-modal">
            <header>
                <div class="formxtra-cf7-deactivate-survey-header">
                    <img src="<?php echo esc_url(FORMXTRACF7_IMAGES . '/menu-icon.png'); ?>" />
                    <h3><?php echo wp_sprintf( '%1$s %2$s', FORMXTRACF7, __( '- Feedback', 'formxtra-cf7' ) ); ?></h3>
                </div>
            </header>
            <div class="formxtra-cf7-deactivate-info">
                <?php echo wp_sprintf( '%1$s %2$s', __( 'If you have a moment, please share why you are deactivating', 'formxtra-cf7' ), FORMXTRACF7 ); ?>
            </div>
            <div class="formxtra-cf7-deactivate-content-wrapper">
                <form action="#" class="formxtra-cf7-deactivate-form-wrapper">
                    <?php foreach($this->get_survey_questions() as $reason_key => $reason){ ?>
                        <div class="formxtra-cf7-deactivate-input-wrapper">
                            <input id="formxtra-cf7-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="formxtra-cf7-deactivate-feedback-dialog-input" type="radio" name="reason_key" value="<?php echo $reason_key; ?>">
                            <label for="formxtra-cf7-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="formxtra-cf7-deactivate-feedback-dialog-label"><?php echo esc_html( $reason['title'] ); ?></label>
							<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
								<input class="formxtra-cf7-deactivate-feedback-text" type="text" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>" />
							<?php endif; ?>
                        </div>
                    <?php } ?>
                    <div class="formxtra-cf7-deactivate-footer">
                        <button id="formxtra-cf7-dialog-lightbox-submit" class="formxtra-cf7-dialog-lightbox-submit"><?php echo esc_html__( 'Submit &amp; Deactivate', 'formxtra-cf7' ); ?></button>
                        <button id="formxtra-cf7-dialog-lightbox-skip" class="formxtra-cf7-dialog-lightbox-skip"><?php echo esc_html__( 'Skip & Deactivate', 'formxtra-cf7' ); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            var deactivate_url = '#';

            jQuery(document).on('click', '#deactivate-formxtra-cf7', function(e) {
                e.preventDefault();
                deactivate_url = e.target.href;
                jQuery('#formxtra-cf7-deactivate-survey-overlay').addClass('formxtra-cf7-deactivate-survey-is-visible');
                jQuery('#formxtra-cf7-deactivate-survey-modal').addClass('formxtra-cf7-deactivate-survey-is-visible');
            });

            jQuery('#formxtra-cf7-dialog-lightbox-skip').on('click', function (e) {
                e.preventDefault();
                window.location.replace(deactivate_url);
            });


            jQuery(document).on('click', '#formxtra-cf7-dialog-lightbox-submit', async function(e) {
                e.preventDefault();

                jQuery('#formxtra-cf7-dialog-lightbox-submit').addClass('formxtra-cf7-loading');

                var $dialogModal = jQuery('.formxtra-cf7-deactivate-input-wrapper'),
                    radioSelector = '.formxtra-cf7-deactivate-feedback-dialog-input';
                $dialogModal.find(radioSelector).on('change', function () {
                    $dialogModal.attr('data-feedback-selected', jQuery(this).val());
                });
                $dialogModal.find(radioSelector + ':checked').trigger('change');


                // Reasons for deactivation
                var deactivation_reason = '';
                var reasonData = jQuery('.formxtra-cf7-deactivate-form-wrapper').serializeArray();

                jQuery.each(reasonData, function (reason_index, reason_value) {
                    if ('reason_key' == reason_value.name && reason_value.value != '') {
                        const reason_input_id = '#formxtra-cf7-deactivate-feedback-' + reason_value.value,
                            reason_title = jQuery(reason_input_id).siblings('label').text(),
                            reason_placeholder_input = jQuery(reason_input_id).siblings('input').val(),
                            format_title_with_key = reason_value.value + ' - '  + reason_placeholder_input,
                            format_title = reason_title + ' - '  + reason_placeholder_input;

                        deactivation_reason = reason_value.value;

                        if ('found_a_better_plugin' == reason_value.value ) {
                            deactivation_reason = format_title_with_key;
                        }

                        if ('need_better_design' == reason_value.value ) {
                            deactivation_reason = format_title_with_key;
                        }

                        if ('other' == reason_value.value) {
                            deactivation_reason = format_title_with_key;
                        }
                    }
                });

                await jQuery.ajax({
                        url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                        method: 'POST',
                        // crossDomain: true,
                        async: true,
                        // dataType: 'jsonp',
                        data: {
                            action: 'formxtra_cf7_deactivation_survey',
                            _wpnonce: '<?php echo esc_js( wp_create_nonce( 'formxtra_cf7_deactivation_nonce' ) ); ?>',
                            deactivation_reason: deactivation_reason
                        },
                        success:function(response){
                            window.location.replace(deactivate_url);
                        }
                });
                return true;
            });

            jQuery('#formxtra-cf7-deactivate-survey-overlay').on('click', function () {
                jQuery('#formxtra-cf7-deactivate-survey-overlay').removeClass('formxtra-cf7-deactivate-survey-is-visible');
                jQuery('#formxtra-cf7-deactivate-survey-modal').removeClass('formxtra-cf7-deactivate-survey-is-visible');
            });
        </script>
        <?php
    }

}
