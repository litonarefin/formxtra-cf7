<?php

namespace FORMXTRACF7\Inc\Addons\Signature;

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Signature
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Signature
{
    private static $instance = null;

    /**
     * Construct Method
     *
     * @return void
     * @author Jewel Theme <support@jeweltheme.com>
     */
    public function __construct()
    {
        // Check: If Enabled
        add_action('wp_enqueue_scripts', [$this, 'formxtra_cf7_frontend_scripts']);

        // Generate Tags on Editor
        add_action('admin_init', [$this, 'formxtra_cf7_generate_tag']);
        add_action('wpcf7_init', [$this, 'formxtra_cf7_signature_shortcode']);
    }

    /**
     * Tags Generator
     */

    public function formxtra_cf7_generate_tag()
    {
        if (!function_exists('wpcf7_add_tag_generator')) {
            return;
        }
        // pretty_log('signateure');
        wpcf7_add_tag_generator(
            'formxtra_cf7_signature',
            esc_html__('Signature', 'formxtra-cf7'),
            'formxtra_cf7-pane-signature',
            array($this, 'formxtra_cf7_pane_signature')
        );
    }

    /**
     * Signature Pane
     *
     * @return void
     */
    public function formxtra_cf7_pane_signature($contact_form, $args = '')
    {
        $args = wp_parse_args($args, array());
        $formxtra_cf7_field_type = 'formxtra_cf7_signature';
?>
        <div class="control-box">
            <fieldset>
                <table class="form-table">
                    <tbody>
                        <div class="formxtra-cf7-doc-notice">
                            <?php echo sprintf(
                                __('Not sure how to set this? Check our step by step  %1s.', 'formxtra-cf7'),
                                '<a href="https://themefic.com/docs/uacf7/free-addons/signature-field/" target="_blank">documentation</a>'
                            ); ?>
                        </div>
                        <tr>
                            <th scope="row"><?php _e('Field Type', 'formxtra-cf7'); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php _e('Field Type', 'formxtra-cf7'); ?></legend>
                                    <label><input type="checkbox" name="required" value="on"><?php _e('Required Field', 'formxtra-cf7'); ?></label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'formxtra-cf7')); ?></label></th>
                            <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="tag-generator-panel-text-class"><?php echo esc_html__('Class attribute', 'formxtra-cf7'); ?></label></th>
                            <td><input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-text-class"></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr($formxtra_cf7_field_type); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" id="prevent_multiple" value="<?php echo esc_attr(__('Insert Tag', 'formxtra-cf7')); ?>" />
            </div>
        </div>
    <?php
    }


    /**
     * Signature Shortcode
     */
    public function formxtra_cf7_signature_shortcode()
    {

        wpcf7_add_form_tag(
            array('formxtra_cf7_signature', 'formxtra_cf7_signature*'),
            array($this, 'formxtra_cf7_signature_tag_handler_callback'),
            array(
                'name-attr' => true,
                'file-uploading' => true
            )
        );
    }

    public function formxtra_cf7_signature_tag_handler_callback($tag)
    {

        if (empty($tag->name)) {
            return '';
        }

        /** Enable / Disable Submission ID */
        $wpcf7                    = \WPCF7_ContactForm::get_current();
        $formid                   = $wpcf7->id();
        // $uacf7_signature_settings = get_post_meta($formid, 'uacf7_signature_settings', true);
        // $uacf7_signature_enable   = $uacf7_signature_settings['uacf7_signature_enable'];
        // $bg_color                 = $uacf7_signature_settings['uacf7_signature_bg_color'];
        // $pen_color                = $uacf7_signature_settings['uacf7_signature_pen_color'];

        pretty_log('$formid', $formid);

        // if ($uacf7_signature_enable != 'on' || $uacf7_signature_enable === '') {
        //     return;
        // }
        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type);

        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }


        $atts = array();

        $atts['class']     = $tag->get_class_option($class);
        $atts['id']        = $tag->get_id_option();
        $atts['pen-color'] = esc_attr($pen_color);
        $atts['bg-color']  = esc_attr($bg_color);
        $atts['tabindex']  = $tag->get_option('tabindex', 'signed_int', true);

        if ($tag->is_required()) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $atts['name'] = $tag->name;

        $atts = wpcf7_format_atts($atts);

        ob_start();

    ?>
        <span class="wpcf7-form-control-wrap <?php echo sanitize_html_class($tag->name); ?>" data-name="<?php echo sanitize_html_class($tag->name); ?>">
            <input hidden type="file" id="img_id_special" <?php echo $atts; ?>>
            <div>
                <div id="signature-pad">
                    <canvas id="signature-canvas"></canvas>
                </div>
                <span id="confirm_message"></span>
                <div class="control_div">
                    <button id="clear-button">Clear</button>
                    <button id="convertButton">Confirm Signature</button>
                </div>
            </div>
        </span>

<?php
        $output = ob_get_clean();

        return $output;
    }

    /**
     * Frontend Scripts
     *
     * @return void
     */
    public function formxtra_cf7_frontend_scripts()
    {
        wp_enqueue_script('formxtra-cf7-signature', FORMXTRACF7_ASSETS . 'vendors/signature.js', ['jquery'], FORMXTRACF7_VER, true);
        wp_enqueue_script('formxtra-cf7-frontend', FORMXTRACF7_ASSETS . 'js/formxtra-cf7-frontend.js', ['jquery'], FORMXTRACF7_VER, true);
    }


    /**
     * Returns the singleton instance of the class.
     */
    public static function get_instance()
    {
        if (!isset(self::$instance) && !(self::$instance instanceof Signature)) {
            self::$instance = new Signature();
        }
        return self::$instance;
    }
}
