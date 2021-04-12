<?php
/**
 * Admin class
 *
 * Manage all admin related functionality
 *
 * @package ChiliDevs\WpFormSms
 */

declare(strict_types=1);

namespace ChiliDevs\WpFormSms\Admin;

use  ChiliDevs\WpFormSms\Admin\SettingsAPI;
use function ChiliDevs\WpFormSms\plugin;
/**
 * Admin class.
 *
 * @package ChiliDevs\WpFormSms\Admin
 */
class Admin {
	/**
	 * Holde Settings API class
	 *
	 * @var $settings_api
	 *
	 * @since 1.0.0
	 */
	private $settings_api;

	/**
	 * Load automatically when class initiate
	 *
	 * @return void
	 */
	public function __construct() {
		$this->settings_api = new SettingsAPI();
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_menu', [ $this, 'load_menu' ], 12 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'chili_settings_form_bottom_wpforms_sms_settings', [ $this, 'settings_gateway_fields' ] );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * Allows plugin assets to be loaded.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'admin-wpforms-sms-scripts', plugin()->assets_dir . '/build/js/admin.build.js', array( 'jquery' ), false, true );
		wp_localize_script( 'admin-wpforms-sms-scripts', 'wcmessagemedia', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );
	}
	/**
	 * Initialize the settings.
	 *
	 * @return void
	 */
	public function admin_init() {
		// Set the settings.
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );

		// Initialize settings.
		$this->settings_api->admin_init();
	}

	/**
	 * Load SMS setting menu under cf7 main menu
	 *
	 * @since 1.0.0
	 */
	public function load_menu() {
		add_submenu_page(
			'wpforms-overview',
			__( 'SMS Settings', 'wp-form-sms' ),
			__( 'SMS Settings', 'wp-form-sms' ),
			'manage_options',
			'wpforms-sms-settings',
			[ $this, 'wpforms_sms_settings_page' ]
		);
	}

	/**
	 * Plugin settings sections.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_settings_sections() {
		$sections = [
			[
				'id'    => 'wpforms_sms_settings',
				'title' => '',
				'name'  => __( 'SMS Settings', 'wp-form-sms' ),
				'icon'  => 'dashicons-admin-tools',
			],
		];

		return apply_filters( 'wpforms_sms_get_settings_sections', $sections );
	}

	/**
	 * Returns all the settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array settings fields
	 */
	public function get_settings_fields() {
		$settings_fields = [
			'wpforms_sms_settings' => [
				[
					'name'    => 'sms_gateway',
					'label'   => __( 'Select Gateway', 'wp-form-sms' ),
					'desc'    => __( 'Select your sms gateway', 'wp-form-sms' ),
					'type'    => 'select',
					'default' => '-1',
					'options' => $this->get_sms_gateway(),
				],
			],
		];

		return apply_filters( 'wpforms_sms_get_settings_fields', $settings_fields );
	}

	/**
	 * Render setting content page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wpforms_sms_settings_page() {
		?>
			<div class="wrap">
				<h1><?php esc_html_e( 'SMS Settings', 'wp-form-sms' ); ?> </h1>
				<hr>
				<?php
					$this->settings_api->show_navigation();
					$this->settings_api->show_forms();
				?>
			</div>
		<?php
	}

	/**
	 * Get sms Gateway settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_sms_gateway() {
		$gateway = array(
			''          => __( '--select--', 'wp-form-sms' ),
			'nexmo'     => __( 'Vonage(Nexmo)', 'wp-form-sms' ),
			'clicksend' => __( 'ClickSend', 'wp-form-sms' ),
		);

		return apply_filters( 'wpforms_sms_gateway', $gateway );
	}

	/**
	 * Render settings gateway extra fields
	 *
	 * @since 1.0.0
	 *
	 * @return void|HTML
	 */
	public function settings_gateway_fields() {
		// Nexomo Properties.
		$nexmo_api        = wpforms_sms_get_option( 'nexmo_api', 'wpforms_sms_settings', '' );
		$nexmo_api_secret = wpforms_sms_get_option( 'nexmo_api_secret', 'wpforms_sms_settings', '' );
		$nexmo_from_name  = wpforms_sms_get_option( 'nexmo_from_name', 'wpforms_sms_settings', '' );
		$nexmo_helper = sprintf( __( 'Enter your Vonage(Nexmo) details. Please visit <a href="%s" target="_blank">%s</a> and get your api keys and options', 'wp-form-sms' ), 'https://dashboard.nexmo.com/login', 'Nexmo' );

		// Clicksend properties.
		$clicksend_username = wpforms_sms_get_option( 'clicksend_username', 'wpforms_sms_settings', '' );
		$clicksend_api      = wpforms_sms_get_option( 'clicksend_api', 'wpforms_sms_settings', '' );
		$clicksend_helper = sprintf( __( 'Enter ClickSend details. Please visit <a href="%s" target="_blank">%s</a> and get your username and api keys', 'wp-form-sms' ), 'https://dashboard.clicksend.com/signup', 'Clicksend' );

		?>

		<!-- start nexomo block -->
		<div class="nexmo_wrapper hide_class">
			<hr>
			<p style="margin-top:15px; margin-bottom:0px; font-style: italic; font-size: 14px;">
			<strong><?php echo wp_kses_post( $nexmo_helper ); ?></strong>
			</p>
			<table class="form-table">
				<tr valign="top">
				<th scrope="row"><?php esc_html_e( 'Vonage(Nexmo) API', 'wp-form-sms' ) ?></th>
					<td>
						<input type="text" class="regular-text" name="wpforms_sms_settings[nexmo_api]" id="wpforms_sms_settings[nexmo_api]" value="<?php echo esc_attr( $nexmo_api ); ?>">
						<p class="description"><?php esc_html_e( 'Enter Vonage(Nexmo) API key', 'wp-form-sms' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scrope="row"><?php esc_html_e( 'Vonage(Nexmo) API Secret', 'wp-form-sms' ) ?></th>
					<td>
						<input type="text" class="regular-text" name="wpforms_sms_settings[nexmo_api_secret]" id="wpforms_sms_settings[nexmo_api_secret]" value="<?php echo esc_attr( $nexmo_api_secret ); ?>">
						<p class="description"><?php esc_html_e( 'Enter Vonage(Nexmo) API secret', 'wp-form-sms' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scrope="row"><?php esc_html_e( 'Vonage(Nexmo) From Name', 'wp-form-sms' ) ?></th>
					<td>
						<input type="text" class="regular-text" name="wpforms_sms_settings[nexmo_from_name]" id="wpforms_sms_settings[nexmo_from_name]" value="<?php echo esc_attr( $nexmo_from_name ); ?>">
						<p class="description"><?php esc_html_e( 'From which name the message will be sent to the users ( Default : VONAGE )', 'wp-form-sms' ); ?></p>
					</td>
				</tr>
			</table>
		</div>
		<!-- End nexomo block -->

		<!-- Start clicksend Block -->
		<div class="clicksend_wrapper hide_class">
			<hr>
			<p style="margin-top:15px; margin-bottom:0px; font-style: italic; font-size: 14px;">
				<strong><?php echo wp_kses_post( $clicksend_helper ); ?></strong>
			</p>
			<table class="form-table">
				<tr valign="top">
					<th scrope="row"><?php esc_html_e( 'ClickSend Username', 'wp-form-sms' ) ?></th>
					<td>
						<input type="text" class="regular-text" name="wpforms_sms_settings[clicksend_username]" id="wpforms_sms_settings[clicksend_username]" value="<?php echo esc_attr( $clicksend_username ); ?>">
						<p class="description"><?php esc_html_e( 'Enter ClickSend Username', 'wp-form-sms' ); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scrope="row"><?php esc_html_e( 'ClickSend API key', 'wp-form-sms' ) ?></th>
					<td>
						<input type="text" class="regular-text" name="wpforms_sms_settings[clicksend_api]" id="wpforms_sms_settings[clicksend_api]" value="<?php echo esc_attr( $clicksend_api ); ?>">
						<p class="description"><?php esc_html_e( 'Enter ClickSend API', 'wp-form-sms' ); ?></p>
					</td>
				</tr>

			</table>

		</div>
		<!-- End Clicksend Block -->
		<?php
	}
}
