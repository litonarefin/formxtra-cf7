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
			add_action('wpcf7_admin_misc_pub_section', array($this, 'before_save_button'));
		}

		public function notice_header()
		{
			return '';
		}

		/**
		 * Notice footer
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function notice_footer()
		{
			return '';
		}

		public function notice_content()
		{
			echo 'thait is scontent';
		}

		public function intervals()
		{
			return array(5, 4, 10, 20, 15, 25, 30);
		}

		public function before_save_button()
		{
			$deal_link = sanitize_url('https://themefic.com/deals/');
			echo '<style>
            .formxtra_cf7_post_pub_notice a:focus {
                box-shadow: none;
            }

            .formxtra_cf7_post_pub_notice a {
                display: inline-block;
            }

            #uacf7_black_friday_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #uacf7_black_friday_docs .postbox-header {
                display: none;
                visibility: hidden;
            }

            .formxtra_cf7_post_pub_notice {
                position: relative;
            }

            .formxtra_cf7_post_pub_notice_dismiss {
                position: ;
                z-index: 1;
            }
        </style>';
?>

			<div class="formxtra_cf7_post_pub_notice" style="text-align: center; overflow: hidden; margin: 10px;">
				<a href="<?php echo $deal_link; ?>" target="_blank">
					<img style="width: 100%;" src="<?php echo sanitize_url('https://themefic.com/wp-content/uploads/2023/11/UACF7_BlackFriday_Square_banner.png') ?>" alt="">
				</a>
				<button type="button" class="notice-dismiss formxtra_cf7_post_pub_notice_dismiss">
					<span class="screen-reader-text">
						Dismiss this notice.
					</span>
				</button>
			</div>
			<script>
				jQuery(document).ready(function($) {
					$(document).on('click', '.formxtra_cf7_post_pub_notice_dismiss', function(event) {
						jQuery('.back_friday_2022_preview').css('display', 'none')
						data = {
							action: 'uacf7_black_friday_notice_cf7_dismiss_callback',
						};

						$.ajax({
							url: ajaxurl,
							type: 'post',
							data: data,
							success: function(data) {
								;
							},
							error: function(data) {}
						});
					});
				});
			</script>
<?php
		}
	}
}
