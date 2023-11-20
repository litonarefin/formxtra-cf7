<?php
namespace FORMXTRACF7\Inc\Classes\Notifications;

use FORMXTRACF7\Inc\Classes\Notifications\Base\Date;
use FORMXTRACF7\Libs\Helper;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Notification Class
 *
 * Jewel Theme <support@jeweltheme.com>
 */
class Notifications {

	use Date;

	public $manager;

	public $conflict_days = 5;

	public $slug;

	/**
	 * Constructor method
	 */
	public function __construct() {
		$this->manager = new Manager();

		$this->slug = Helper::formxtra_cf7_slug_cleanup();

		add_action( 'admin_notices', array( $this, 'setup_notifications' ) );

		add_action( 'formxtra_cf7_display_notice', array( $this, 'display_notice' ), 10, 2 );
		add_action( 'formxtra_cf7_display_popup', array( $this, 'display_popup' ), 10, 2 );

		add_action( 'wp_ajax_formxtra_cf7_notification_action', array( $this, 'notification_action' ) );
	}

	/**
	 * Notification Action
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function notification_action() {
		check_ajax_referer( 'formxtra_cf7_notification_nonce' );

		$action_type       = ! empty( $_REQUEST['action_type'] ) ? sanitize_key( $_REQUEST['action_type'] ) : '';
		$notification_type = ! empty( $_REQUEST['notification_type'] ) ? sanitize_key( $_REQUEST['notification_type'] ) : '';
		$trigger_time      = ! empty( $_REQUEST['trigger_time'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['trigger_time'] ) ) : '';

		$exec_notifications = $this->manager->get_exec_notifications( $trigger_time, $notification_type );

		// No Executable Notifications found .
		if ( empty( $exec_notifications ) ) {
			die( 0 );
		}

		$count = 0;

		foreach ( $exec_notifications as $index => $notification ) {
			if ( 0 === $index ) {
				if ( 'disable' === $action_type ) {
					$notification->is_active = false;
				}
				$notification->fire( $trigger_time, $notification_type )->save();
			} else {
				$count++;
				$notification->maybe_delay( $this->date_increment( $trigger_time, $this->conflict_days * $count ) )->save();
			}
		}

		die( 0 );
	}


	/**
	 * Notification Setup
	 *
	 * @param [type] $type .
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function setup_notifications_by_type( $type ) {
		// $trigger_time should be today .
		$trigger_time = $this->current_time();

		// Block if necessary .
		$notification_last_fired = get_option( "formxtra_cf7_{$type}_last_interact" );

		if ( $notification_last_fired ) {
			$notification_enable_date = $this->date_increment( $notification_last_fired, $this->conflict_days );

			if ( $this->date_is_prev( $trigger_time, $notification_enable_date ) ) {
				return;
			}
		}

		// Get Executable Notifications .
		$exec_notifications = $this->manager->get_exec_notifications( $trigger_time, $type );

		// No Executable Notifications found .
		if ( empty( $exec_notifications ) ) {
			return;
		}

		$notification = $exec_notifications[0];

		do_action( "formxtra_cf7_display_{$type}", $notification, $trigger_time );
	}

	/**
	 * Notification setup
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function setup_notifications() {
		$this->setup_notifications_by_type( 'notice' );
		$this->setup_notifications_by_type( 'popup' );
	}



	/**
	 * Display notice
	 *
	 * @param [type] $notice .
	 * @param [type] $trigger_time .
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function display_notice( $notice, $trigger_time ) {
		$notice->notice_header();
		$notice->notice_content();
		$notice->notice_footer();

		$notice->core_script( $trigger_time );
	}

	/**
	 * Display Popup
	 *
	 * @param [type] $popup .
	 * @param [type] $trigger_time .
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function display_popup( $popup, $trigger_time ) {
		$image_url = $popup->get_content( 'image_url' );

		?>

		<div class="formxtra-cf7-popup" id="formxtra-cf7-popup" data-plugin="<?php echo esc_attr( $this->slug ); ?>" tabindex="1">

			<div class="formxtra-cf7-popup-overlay"></div>

			<div class="formxtra-cf7-popup-modal" style="background-image: url('<?php echo esc_url( $image_url ); ?>'); --formxtra-cf7-popup-color: <?php echo esc_attr( $popup->get_content( 'btn_color' ) ); ?>;">

				<!-- close  -->
				<div class="formxtra-cf7-popup-modal-close popup-dismiss">×</div>

				<!-- content section  -->
				<div class="formxtra-cf7-popup-modal-footer">

					<!-- countdown  -->
					<div class="formxtra-cf7-popup-countdown" style="display: none;">
						<span class="formxtra-cf7-popup-countdown-text"><?php echo esc_html__( 'Deal Ends In', 'formxtra-cf7' ); ?></span>
						<div class="formxtra-cf7-popup-countdown-time">
							<div>
								<span data-counter="days">00</span>
								<span><?php echo esc_html__( 'Days', 'formxtra-cf7' ); ?></span>
							</div>
							<span>:</span>
							<div>
								<span data-counter="hours">00</span>
								<span><?php echo esc_html__( 'Hours', 'formxtra-cf7' ); ?></span>
							</div>
							<span>:</span>
							<div>
								<span data-counter="minutes">00</span>
								<span><?php echo esc_html__( 'Minutes', 'formxtra-cf7' ); ?></span>
							</div>
							<span>:</span>
							<div>
								<span data-counter="seconds">00</span>
								<span><?php echo esc_html__( 'Seconds', 'formxtra-cf7' ); ?></span>
							</div>
						</div>
					</div>

					<!-- button  -->
					<a class="formxtra-cf7-popup-button" target="_blank" href="<?php echo esc_url( $popup->get_content( 'button_url' ) ); ?>"><?php echo esc_html( $popup->get_content( 'button_text' ) ); ?></a>
				</div>
			</div>
		</div>

		<script>
			function formxtra_cf7_popup_action(evt, $this, $action_type) {

				evt.preventDefault();

				$this.closest('.formxtra-cf7-popup').fadeOut(200);

				jQuery.post('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
					action: 'formxtra_cf7_notification_action',
					_wpnonce: '<?php echo esc_js( wp_create_nonce( 'formxtra_cf7_notification_nonce' ) ); ?>',
					action_type: $action_type,
					notification_type: 'popup',
					trigger_time: '<?php echo esc_attr( $trigger_time ); ?>'
				});
			}

			// Notice Dismiss
			jQuery('body').on('click', '.formxtra-cf7-popup .popup-dismiss', function(evt) {
				formxtra_cf7_popup_action(evt, jQuery(this), 'dismiss');
			});

			// Notice Disable
			jQuery('body').on('click', '.formxtra-cf7-popup .popup-disable', function(evt) {
				formxtra_cf7_popup_action(evt, jQuery(this), 'disable');
			});
		</script>

		<?php
	}
}