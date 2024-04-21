<?php

namespace FORMXTRACF7\Inc\Addons\Database;

use FORMXTRACF7\Inc\Addons\Database\Formxtra_List_Table;

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Database
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Database
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
        add_action('admin_init', array($this, 'formxtra_cf7_create_database_table'));
        add_action('admin_menu', array($this, 'formxtra_cf7_database_menu'));
        add_action('wpcf7_before_send_mail', array($this, 'formxtra_cf7_save_to_database'));
        add_action('admin_enqueue_scripts', array($this, 'formxtra_cf7_databse_scripts'));
    }

    /**
     * Save to Database
     */
    public function formxtra_cf7_save_to_database($form)
    {

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        global $wpdb;
        $table_name = $wpdb->prefix . 'formxtra_cf7_db';

        $submission   = \WPCF7_Submission::get_instance();
        $ContactForm = \WPCF7_ContactForm::get_instance($form->id());
        $tags = $ContactForm->scan_form_tags();
        $skip_tag_insert = [];
        foreach ($tags as $tag) {
            if ($tag->type == 'formxtra_cf7_step_start' || $tag->type == 'formxtra_cf7_step_end' || $tag->type == 'uarepeater' || $tag->type == 'conditional' || $tag->type == 'formxtra_cf7_conversational_start' || $tag->type == 'formxtra_cf7_conversational_end') {
                if ($tag->name != '') {
                    $skip_tag_insert[] = $tag->name;
                }
            }
        }

        $contact_form_data = $submission->get_posted_data();
        $files            = $submission->uploaded_files();
        $upload_dir    = wp_upload_dir();
        $dir = $upload_dir['basedir'];
        $uploaded_files = [];
        $time_now      = time();
        $data_file      = [];
        $formxtra_cf7_dirname = $upload_dir['basedir'] . '/formxtra_cf7-uploads';
        if (!file_exists($formxtra_cf7_dirname)) {
            wp_mkdir_p($formxtra_cf7_dirname);
        }

        foreach ($_FILES as $file_key => $file) {
            array_push($uploaded_files, $file_key);
        }


        foreach ($files as $file_key => $file) {
            if (!empty($file)) {
                if (in_array($file_key, $uploaded_files)) {
                    $file = is_array($file) ? reset($file) : $file;
                    $dir_link = '/formxtra_cf7-uploads/' . $time_now . '-' . $file_key . '-' . basename($file);
                    copy($file, $dir . $dir_link);
                    array_push($data_file, [$file_key => $dir_link]);
                }
            }
        }

        foreach ($contact_form_data as $key => $value) {
            if (in_array($key, $uploaded_files)) {
                if (empty($data_file)) {
                    $data_file = '';
                } else {
                    $data_file = $data_file[0][$file_key];
                };
                $contact_form_data[$key] = $data_file;
            }
        }
        $data = [
            'status' => 'unread',
        ];
        $data = array_merge($data, $contact_form_data);
        $insert_data = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, $skip_tag_insert)) {

                if (is_array($value)) {
                    $insert_data[$key] = array_map('esc_html', $value);
                } else {
                    $insert_data[$key] = esc_html($value);
                }
            }
        }

        $insert_data = json_encode($insert_data);

        $wpdb->insert($table_name, array(
            'form_id' => $form->id(),
            'form_value' =>  $insert_data,
            'form_date' => current_time('Y-m-d H:i:s'),
        ));
        $formxtra_cf7_db_insert_id = $wpdb->insert_id;

        // Order tracking Action
        do_action('formxtra_cf7_checkout_order_traking', $formxtra_cf7_db_insert_id, $form->id());

        // submission id Action
        do_action('formxtra_cf7_submission_id_insert', $formxtra_cf7_db_insert_id, $form->id(), $contact_form_data, $tags);
    }


    /**
     * Create Database Table
     */
    public function formxtra_cf7_create_database_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'formxtra_cf7_db';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            form_id bigint(20) NOT NULL,
            form_value longtext NOT NULL,
            form_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Database Menu
     */
    public function formxtra_cf7_database_menu()
    {
        add_submenu_page(
            'wpcf7', //parent slug
            __('Formxtra CF7 DB', 'formxtra-cf7'), // page_title
            __('Formxtra CF7 DB', 'formxtra-cf7'), // menu_title
            'manage_options', // capability
            'formxtra-cf7-db', // menu_slug
            array($this, 'formxtra_cf7_databse_settings_page'), // callback function
        );
    }

    /**
     * Admin Database Page
     *
     * @return void
     */
    public function formxtra_cf7_databse_settings_page()
    {

        $form_id  = empty($_GET['form_id']) ? 0 : (int) $_GET['form_id'];
        $pdf  = empty($_GET['pdf']) ? 0 :  $_GET['pdf'];
        $data_id  = empty($_GET['data_id']) ? 0 :  $_GET['data_id'];


        if (!empty($form_id)) {
            $formxtra_cf8_db = new Formxtra_List_Table();
            $formxtra_cf8_db->prepare_items();
?>
            <div class="wrap">
                <div id="icon-users" class="icon32"></div>
                <h2><?php echo esc_html__('Formxtra CF7 Database', 'formxtra-cf7'); ?></h2>
                <?php settings_errors(); ?>
                <form method="post" action="">
                    <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
                    <?php $formxtra_cf8_db->search_box('Search', 'search'); ?>
                    <?php $formxtra_cf8_db->display(); ?>
                </form>
            </div>
            <section class="formxtra_cf7_popup_preview">
                <div class="formxtra_cf7_popup_content">
                    <div id="formxtra_cf7_popup_wrap">
                        <div class="db_popup_view">
                            <div class="close" title="Exit Full Screen">x</div>
                            <div id="db_view_wrap">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php
        } else {

            global $wpdb;
            $list_forms = get_posts(array(
                'post_type'     => 'wpcf7_contact_form',
                'posts_per_page'   => -1
            ));
        ?>

            <div class="wrap formxtra-cf7-admin-cont">
                <h1>
                    <?php echo esc_html__('Formxtra CF7 Database Addon', 'formxtra-cf7'); ?>
                </h1>
                <br>
                <?php settings_errors(); ?>

                <!--Tab buttons start-->
                <div class="formxtra-cf7-tab">
                    <a class="tablinks active" onclick="formxtra_cf7_settings_tab(event, 'formxtra_cf7_addons')"><?php echo esc_html__('Formxtra CF7 Database', 'formxtra-cf7'); ?> </a>
                </div>
                <!--Tab buttons end-->

                <!--Tab Addons start-->
                <div id="formxtra_cf7_addons" class="formxtra-cf7-tabcontent" style="display:block">
                    <table>
                        <tr>
                            <td>
                                <h3><?php echo esc_html__('Select Form :', 'formxtra-cf7'); ?> </h4>
                            </td>
                            <td>
                                <select name="form-id" id="form-id">
                                    <option value="0"><?php echo esc_html__('Select Form', 'formxtra-cf7'); ?> </option>
                                    <?php
                                    foreach ($list_forms as $form) {
                                        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "formxtra_cf7_db WHERE form_id = %d", $form->ID));  // count number of data
                                        echo '<option value="' . esc_attr($form->ID) . '">' . esc_attr($form->post_title) . ' ( ' . $count . ' )</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <button type="submit" class="button-primary" id="database_submit"> <?php echo esc_html__('Submit', 'formxtra-cf7'); ?> </button>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--Tab Addons end-->
            </div>
<?php
        }
    }

    /**
     * Frontend Scripts
     *
     * @return void
     */
    public function formxtra_cf7_databse_scripts()
    {
        wp_enqueue_style('formxtra-cf7-database');
        wp_enqueue_script('formxtra-cf7-database');
    }


    /**
     * Returns the singleton instance of the class.
     */
    public static function get_instance()
    {
        if (!isset(self::$instance) && !(self::$instance instanceof Database)) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
}
