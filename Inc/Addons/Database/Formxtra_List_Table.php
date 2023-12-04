<?php

namespace FORMXTRACF7\Inc\Addons\Database;

/*
* WP_List_Table Class Call
*/

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/*
* extends Formxtra_List_Table class will create the page to load the table
*/

class Formxtra_List_Table extends \WP_List_Table
{

    /**
     * Prepare the items for the table to process
     *
     * @return $columns
     */

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $data = $this->table_data();
        $this->process_bulk_action();
        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = array($columns, $hidden);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */

    public function get_columns()
    {

        $form_id  = empty($_GET['form_id']) ? 0 : (int) $_GET['form_id'];

        $ContactForm = WPCF7_ContactForm::get_instance($form_id);
        $form_fields = $ContactForm->scan_form_tags();

        $columns = [];
        $columns['cb']      = '<input type="checkbox" />';
        $count = count($form_fields);
        $count_item = 4;
        $count_i = 0;

        for ($x = 0; $x < $count; $x++) {

            if ($form_fields[$x]['type'] != 'submit' && $form_fields[$x]['type'] != 'uacf7_step_start' && $form_fields[$x]['type'] != 'uacf7_step_end' && $form_fields[$x]['type'] != 'uarepeater' && $form_fields[$x]['type'] != 'conditional' && $form_fields[$x]['type'] != 'uacf7_conversational_start' && $form_fields[$x]['type'] != 'uacf7_conversational_end') {

                if ($count_i == $count_item) {
                    break;
                }

                $columns[$form_fields[$x]['name']] = $form_fields[$x]['name'];
                $count_i++;
            }
        }

        // Checked Star Review Status
        if ($this->uacf7_star_review_status($form_id) == true) {
            $columns['review_publish'] = 'Review Publish';
        }

        $columns['action'] = 'Action';
        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */

    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Get the table data
     *
     * @return Array
     */

    private function table_data()
    {
        global $wpdb;
        $form_id  = empty($_GET['form_id']) ? 0 : (int) $_GET['form_id'];
        $search       = empty($_REQUEST['s']) ? false :  esc_sql($_REQUEST['s']);
        $upload_dir    = wp_upload_dir();
        $dir = $upload_dir['baseurl'];
        $replace_dir = '/uacf7-uploads/';
        $data = [];
        if (isset($search) && !empty($search)) {

            $form_data = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formxtra_cf7_db WHERE form_id = %d AND form_value LIKE '%$search%' ORDER BY id DESC", $form_id)
            );
        } else {
            $form_data = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formxtra_cf7_db WHERE form_id = %d ORDER BY id DESC",  $form_id)
            );
        }
        foreach ($form_data as $fdata) {
            $f_data = [];
            $field_data =  json_decode($fdata->form_value);
            $repetar_value = '';
            $repetar_key = '';
            $enable_pdf = !empty(get_post_meta($fdata->form_id, 'uacf7_enable_pdf_generator', true)) ? get_post_meta($fdata->form_id, 'uacf7_enable_pdf_generator', true) : '';

            if ($enable_pdf == 'on' && uacf7_checked('uacf7_enable_pdf_generator_field') != '') {
                $pdf_btn =  "<button data-form-id='" . esc_attr($fdata->form_id) . "' data-id='" . esc_attr($fdata->id) . "' data-value='" . esc_html($fdata->form_value) . "' class='button-primary uacf7-db-pdf'> Export as PDF</button>";
            } else {
                $pdf_btn = '';
            }

            $order_btn = isset($field_data->order_id) && $field_data->order_id != 0 ? "<a target='_blank' href='" . admin_url('post.php?post=' . $field_data->order_id . '&action=edit') . "' class='button-primary uacf7-db-pdf'> View Order</a>" : '';
            foreach ($field_data as $key => $value) {
                if (is_array($value)) {
                    $value = implode(", ", $value);
                } else {
                    $value = esc_html($value);
                }

                if (strstr($value, $replace_dir)) {
                    $value = str_replace($replace_dir, "", $value);
                    $f_data[$key] = '<a href="' . $dir . $replace_dir . $value . '" target="_blank">' . esc_html($value) . '</a>';
                } else {
                    $f_data[$key] = esc_html($value);
                }
                if (strpos($key, '__') !== false) {
                    $repetar_key = explode('__', $key);
                    $repetar_key = $repetar_key[0];
                    $f_data[$repetar_key] = esc_html($value);
                }
            }
            $f_data['id']      = $fdata->id;

            // Checked Star Review Status
            if ($this->uacf7_star_review_status($form_id) == true) {
                $checked = $fdata->is_review == 1 ? 'checked' : '';
                $f_data['review_publish'] = '<label class="uacf7-admin-toggle1 uacf7_star_label" for="uacf7_review_status_' . esc_attr($fdata->id) . '">
                <input type="checkbox" class="uacf7-admin-toggle__input star_is_review" value="' . esc_attr($fdata->id) . '"  name="uacf7_review_status_' . esc_attr($fdata->id) . '" id="uacf7_review_status_' . esc_attr($fdata->id) . '" ' . esc_attr($checked) . '>
                <span class="uacf7-admin-toggle-track"><span class="uacf7-admin-toggle-indicator"><span class="checkMark"><svg viewBox="0 0 24 24" id="ghq-svg-check" role="presentation" aria-hidden="true"><path d="M9.86 18a1 1 0 01-.73-.32l-4.86-5.17a1.001 1.001 0 011.46-1.37l4.12 4.39 8.41-9.2a1 1 0 111.48 1.34l-9.14 10a1 1 0 01-.73.33h-.01z"></path></svg></span></span></span>
            </label>';
            }

            $f_data['action'] = "<button data-id='" . esc_attr($fdata->id) . "' data-value='" . esc_html($fdata->form_value) . "' class='button-primary uacf7-db-view'>" . __('View', 'ultimate-addons-cf7') . "</button>" . $pdf_btn . $order_btn;
            $data[] = $f_data;
        }
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     */

    public function column_default($item, $column_name)
    {
        // echo "<pre>";
        // print_r($item);
        if (isset($item[$column_name])) {
            return $item[$column_name];
        }
    }


    /**
     * Single row add css class for unread data
     *
     */

    public function single_row($item)
    {
        $cssClass = ($item['status'] == 'unread') ? 'unread' : 'read';
        echo '<tr class="' . $cssClass . '">';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    /**
     * Culumn checkbox for data filter
     *
     */

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="uacf7_db_id[]" value="%1$s" />',
            $item['id']
        );
    }

    /**
     * Bulk action
     *
     */

    function get_bulk_actions()
    {
        $actions = array(
            'delete' => __('Delete', 'visual-form-builder'),
        );
        return $actions;
    }


    protected function bulk_actions($which = '')
    {
        if (is_null($this->_actions)) {
            $this->_actions = $this->get_bulk_actions();

            /**
             * Filters the items in the bulk actions menu of the list table.
             *
             * The dynamic portion of the hook name, `$this->screen->id`, refers
             * to the ID of the current screen.
             *
             * @since 3.1.0
             * @since 5.6.0 A bulk action can now contain an array of options in order to create an optgroup.
             *
             * @param array $actions An array of the available bulk actions.
             */
            $this->_actions = apply_filters("bulk_actions-{$this->screen->id}", $this->_actions); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

            $two = '';
        } else {
            $two = '2';
        }

        if (empty($this->_actions)) {
            return;
        }

        echo '<label for="bulk-action-selector-' . esc_attr($which) . '" class="screen-reader-text">' . __('Select bulk action') . '</label>';
        echo '<select name="action' . $two . '" id="bulk-action-selector-' . esc_attr($which) . "\">\n";
        echo '<option value="-1">' . __('Bulk actions') . "</option>\n";

        foreach ($this->_actions as $key => $value) {
            if (is_array($value)) {
                echo "\t" . '<optgroup label="' . esc_attr($key) . '">' . "\n";

                foreach ($value as $name => $title) {
                    $class = ('edit' === $name) ? ' class="hide-if-no-js"' : '';

                    echo "\t\t" . '<option value="' . esc_attr($name) . '"' . $class . '>' . $title . "</option>\n";
                }
                echo "\t" . "</optgroup>\n";
            } else {
                $class = ('edit' === $key) ? ' class="hide-if-no-js"' : '';

                echo "\t" . '<option value="' . esc_attr($key) . '"' . $class . '>' . $value . "</option>\n";
            }
        }

        echo "</select>\n";

        submit_button(__('Apply'), 'action', '', false, array('id' => "doaction $two"));
        echo "<button  style=' margin-left:5px;' class='button uacf7-db-export-csv'> Export CSV</button>";
        echo "\n";
    }


    /**
     * Bulk action Filter
     *
     */

    function process_bulk_action()
    {
        global $wpdb;
        if ('delete' === $this->current_action()) {
            $ids = isset($_POST['uacf7_db_id']) ? $_POST['uacf7_db_id'] : array();
            foreach ($ids as $id) {
                $id = absint($id);
                $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "formxtra_cf7_db WHERE id = %d", $id));
            }
        }
    }

    // Checked Star Review Status Function
    public function uacf7_star_review_status($id)
    {
        if (class_exists('UACF7_STAR_RATING_PRO') && class_exists('UACF7_STAR_RATING')) {
            return apply_filters('uacf7_star_review_status', false, $id); // checked star review status
        } else {
            return false;
        }
    }
}
