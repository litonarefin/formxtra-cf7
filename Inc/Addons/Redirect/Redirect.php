<?php

namespace FORMXTRACF7\Inc\Addons\Redirect;

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Redirect
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Redirect
{
	private static $instance = null;

	protected $redirect_url;

	protected $enqueue_new_tab_script;

	/**
	 * Construct Method
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function __construct()
	{
		// Check: If Enabled
		// $this->redirect_init();
		add_action('wpcf7_editor_panels', array($this, 'formxtra_cf7_redirect_add_panel'));
		// add_action('wpcf7_after_save', array($this, 'formxtra_cf7_redirect_save_meta'));
		add_action('wpcf7_submit', array($this, 'formxtra_cf7_redirect_redirect'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_script'));
	}

	/**
	 * Script for Redirect
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function enqueue_script()
	{
		if (isset($this->enqueue_new_tab_script) && $this->enqueue_new_tab_script) {
			wp_add_inline_script('formxtra-cf7-redirect', 'window.open("' . $this->redirect_url . '");');
		}
	}


	/**
	 * Add Tab Panel
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function formxtra_cf7_redirect_add_panel($panels)
	{
		$panels['formxtra-cf7-redirect-panel'] = array(
			'title'    => __('FXCF7 Redirect', 'formxtra-cf7'),
			'callback' => array($this, 'formxtra_cf7_create_redirect_panel_fields'),
		);
		return $panels;
	}

	/*
    * Fields array
    */
	public function fields()
	{
		$fields = array(
			array(
				'name'  => 'uacf7_redirect_to_type',
				'type'  => 'radio',
			),
			array(
				'name'  => 'page_id',
				'type'  => 'number',
			),
			array(
				'name'  => 'external_url',
				'type'  => 'url',
			),
			array(
				'name'  => 'target',
				'type'  => 'checkbox',
			),
		);
		return $fields;
	}


	public function uacf7_get_options($post_id)
	{
		$fields = $this->fields();
		foreach ($fields as $field) {
			$values[$field['name']] = get_post_meta($post_id, 'uacf7_redirect_' . $field['name'], true);
		}
		return $values;
	}

	/**
	 * Submissions
	 *
	 * @param [type] $post
	 *
	 * @return void
	 */
	public function formxtra_cf7_redirect_redirect()
	{
		// Check: If Enabled

		// $this->fields = $this->uacf7_get_options( $contact_form->id() );
		// pretty_log('$submission before');

		// if ( ! empty( $this->fields ) && ! WPCF7_Submission::is_restful() ) {
		if (!\WPCF7_Submission::is_restful()) {
			$submission = \WPCF7_Submission::get_instance();
			if ($submission->get_status() === 'mail_sent') {

				// Open link in a new tab
				// if ( isset( $this->redirect_url ) && $this->redirect_url ) {
				// 	$this->enqueue_new_tab_script = true;
				wp_redirect('https://wpadminify.com');
				exit;
				// }
			}
		}
	}


	/*
    * Redirect Fields Function
    */
	public function formxtra_cf7_create_redirect_panel_fields($post)
	{ ?>
		<h2><?php echo esc_html__('FXCF7 Redirection Settings', 'formxtra-cf7'); ?></h2>
		<p>
			<?php echo esc_html__('This feature will help you to redirect contact form 7 after submission. You can Redirect users to a Thank you page or External page after user fills up the form.', 'formxtra-cf7'); ?>
		</p>

		<fieldset>
			<?php
			$options = $this->uacf7_get_options($post->id());
			$uacf7_redirect_to_type = !empty($options['uacf7_redirect_to_type']) ? $options['uacf7_redirect_to_type'] : 'to_page';
			$uacf7_redirect_enable = get_post_meta($post->id(), 'uacf7_redirect_enable', true);
			?>

			<p>
				<label for="uacf7_redirect_enable">
					<input class="uacf7_redirect_enable" id="uacf7_redirect_enable" name="uacf7_redirect_enable" type="checkbox" value="yes" <?php checked('yes', $uacf7_redirect_enable, true); ?>> <?php echo esc_html__('Enable redirection'); ?>
				</label><br>
			</p>

			<div class="uacf7_default_redirect_wraper" style="margin: 20px;">
				<p>
					<label for="uacf7_redirect_to_page">
						<input class="uacf7_redirect_to_type" id="uacf7_redirect_to_page" name="uacf7_redirect[uacf7_redirect_to_type]" type="radio" value="to_page" <?php checked('to_page', $uacf7_redirect_to_type, true); ?>> <?php echo esc_html__('Redirect to page'); ?>
					</label><br>
					<label for="uacf7_redirect_to_url">
						<input class="uacf7_redirect_to_type" id="uacf7_redirect_to_url" name="uacf7_redirect[uacf7_redirect_to_type]" type="radio" value="to_url" <?php checked('to_url', $uacf7_redirect_to_type, true); ?>> <?php echo esc_html__('Redirect to external URL'); ?>
					</label>
				</p>
				<p class="uacf7_redirect_to_page">
					<label for="uacf7-redirect-page">
						<?php esc_html_e('Select a page to redirect', 'formxtra-cf7'); ?>
					</label><br>
					<?php
					$pages = get_posts(array(
						'post_type'        => 'page',
						'posts_per_page'   => -1,
						'post_status'      => 'published',
					));
					?>
					<select name="uacf7_redirect[page_id]" id="uacf7-redirect-page">
						<option value="0" <?php selected(0, $options['page_id']); ?>>
							<?php echo esc_html__('Choose Page', 'formxtra-cf7'); ?>
						</option>

						<?php foreach ($pages as $page) : ?>

							<option value="<?php echo esc_attr($page->ID); ?>" <?php selected($page->ID, $options['page_id']); ?>>
								<?php echo esc_html($page->post_title); ?>
							</option>

						<?php endforeach; ?>
					</select>
				</p>
				<p class="uacf7_redirect_to_url">
					<input type="url" id="uacf7-external-url" name="uacf7_redirect[external_url]" class="large-text" value="<?php echo esc_html($options['external_url']); ?>" placeholder="<?php echo esc_html__('Enter an external URL', 'formxtra-cf7'); ?>">
				</p>

			</div>

			<?php ob_start(); ?>

			<!--Start Conditional redirect-->
			<div class="uacf7_conditional_redirect_wraper" style="margin: 20px;">
				<div class="uacf7_conditional_redirect_add_btn">
					<a href="#" class="button-primary uacf7_cr_btn">+ Add Condition</a> <a style="color:red" target="_blank" href="https://cf7addons.com/">(Pro)</a>

					<!--Start New row-->
					<div style="display:none" class="uacf7_cr_copy">
						<li class="uacf7_conditional_redirect_condition">
							<span><?php echo esc_html__('If', 'formxtra-cf7'); ?></span>
							<span>
								<select class="uacf7-field">
									<?php
									$all_fields = array();
									$all_fields = $post->scan_form_tags();
									?>
									<option value=""><?php echo esc_html('-- Select field --', 'formxtra-cf7') ?></option>
									<?php
									foreach ($all_fields as $tag) {
										if ($tag['name'] == '') continue;
									?>
										<?php
										if ($tag['type'] == 'checkbox') {

											$tag_name = $tag['name'] . '[]';
										} else {

											$tag_name = $tag['name'];
										}
										?>
										<option><?php echo esc_html($tag['name']); ?></option>

									<?php
									}
									?>
								</select>
							</span>
							<span> <?php echo esc_html__('Value == ', 'formxtra-cf7'); ?> </span>
							<span> <input type="text" placeholder="Value"> </span>
							<span> <?php echo esc_html__('Redirect to', 'formxtra-cf7'); ?> </span>
							<span><input type="text" placeholder="Redirect URL"></span>
							<spna><a href="#" class="uacf7_cr_remove_row">x</a></spna>
						</li>
					</div>
					<!--End New row-->

				</div>

				<ul class="uacf7_conditional_redirect_conditions">
					<li class="uacf7_conditional_redirect_condition">
						<span><?php echo esc_html__('If', 'formxtra-cf7'); ?></span>
						<span>
							<select class="uacf7-field">
								<?php
								$all_fields = array();
								$all_fields = $post->scan_form_tags();
								?>
								<option value=""><?php echo esc_html('-- Select field --', 'formxtra-cf7') ?></option>
								<?php
								foreach ($all_fields as $tag) {
									if ($tag['name'] == '') continue;
								?>
									<?php
									if ($tag['type'] == 'checkbox') {

										$tag_name = $tag['name'] . '[]';
									} else {

										$tag_name = $tag['name'];
									}
									?>
									<option><?php echo esc_html($tag['name']); ?></option>

								<?php
								}
								?>
							</select>
						</span>
						<span><?php echo esc_html__(' Value == ', 'formxtra-cf7'); ?> </span>
						<span> <input type="text" placeholder="Value"> </span>
						<span><?php echo esc_html__('  Redirect to ', 'formxtra-cf7'); ?></span>
						<span><input type="text" placeholder="Redirect URL"></span>
						<spna><a href="#" class="uacf7_cr_remove_row">x</a></spna>
					</li>
				</ul>

			</div>
			<!--End Conditional redirect-->

			<?php

			$uacf7_cr_pro_fields = ob_get_clean();

			echo apply_filters('uacf7_cr_pro_fields', $uacf7_cr_pro_fields, $post);
			?>

			<?php ob_start(); ?>
			<p>
				<label for="uacf7_redirect_type">
					<input class="uacf7_redirect_type" id="uacf7_redirect_type" name="" type="checkbox" value="yes"> <?php echo esc_html__('Conditional Redirect'); ?>
				</label> <a style="color:red" target="_blank" href="https://cf7addons.com/">(Pro)</a><br>
			</p>
			<?php
			$uacf7_redirect_type_html = ob_get_clean();
			echo apply_filters('uacf7_redirect_type_field', $uacf7_redirect_type_html, $post);
			?>

			<p>
				<input id="uacf7_tab_target" type="checkbox" name="uacf7_redirect[target]" <?php checked($options['target'], 'on', true); ?>>
				<label for="uacf7_tab_target"><?php echo esc_html__('Open page in a new tab', 'formxtra-cf7'); ?></label>
			</p>

			<?php ob_start(); ?>
			<p>
				<input id="uacf7_redirect_tag_support" type="checkbox" name="">
				<label for="uacf7_redirect_tag_support"><?php echo esc_html__('Tags support to redirect URL', 'formxtra-cf7'); ?></label> <a style="color:red" target="_blank" href="https://cf7addons.com/">(Pro)</a>
				<span style="display:block;font-size:13px;color:#666"><?php echo esc_html__('Enable support contact form 7 fields tags to use on custom redirect URL. Such as', 'formxtra-cf7'); ?> - www.yourdomain.com/?name=[your-name]</span>
			</p>
			<?php
			$uacf7_redirect_tag_support = ob_get_clean();
			echo apply_filters('uacf7_redirect_tag_support', $uacf7_redirect_tag_support, $post);
			?>

			<div class="uacf7-doc-notice">

				<?php echo sprintf(
					__('Not sure how to set this? Check our step by step documentation on  %1s, %2s and %3s .', 'formxtra-cf7'),
					'<a href="https://themefic.com/docs/uacf7/free-addons/redirection-for-contact-form-7/" target="_blank">Redirect to a Page or External URL</a>',
					'<a href="https://themefic.com/docs/uacf7/pro-addons/conditional-redirect-for-contact-form-7/" target="_blank">Conditional Redirect</a>',
					'<a href="https://themefic.com/docs/uacf7/pro-addons/contact-form-7-whatsapp-integration-and-tag-support/" target="_blank">Tag Support</a>'
				); ?>
			</div>
		</fieldset>

<?php
		wp_nonce_field('uacf7_redirection_nonce_action', 'uacf7_redirect_nonce');
	}


	/**
	 * Add Panel
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function redirect_init()
	{
	}



	/**
	 * Returns the singleton instance of the class.
	 */
	public static function get_instance()
	{
		if (!isset(self::$instance) && !(self::$instance instanceof Redirect)) {
			self::$instance = new Redirect();
			// self::$instance->redirect_init();
		}

		return self::$instance;
	}
}
