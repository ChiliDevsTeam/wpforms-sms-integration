<?php
/**
 * FormSettings  class
 *
 * Manage  FormSettings related functionality on Wp Form
 *
 * @package ChiliDevs\WpFormSms
 */

declare(strict_types=1);

namespace ChiliDevs\WpFormSms\Admin;

use WP_Error;

/**
 * FormSettings Class.
 *
 * @package ChiliDevs\WpFormSms\Admin
 */
class FormSettings {

	/**
	 * Load automatically when class initiate
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'wpforms_builder_settings_sections', [ $this, 'form_sms_settings_section' ], 20, 2 );
		add_action( 'wpforms_form_settings_panel_content', [ $this, 'form_sms_settings_content' ], 10, 1 );
		add_action( 'wpforms_process_complete', [ $this, 'send_sms' ], 10, 4 );
	}

	/**
	 *  Load automatically when class initiate
	 *
	 * @param  array $sections Settings Array.
	 * @param  array $form_data Form data Array.
	 *
	 * @return $sections
	 */
	public function form_sms_settings_section( $sections, $form_data ) {
			$sections['sms_settings'] = __( 'Sms Settings', 'wp-form-sms' );
			return $sections;
	}

	/**
	 *  Section Content
	 *
	 * @param  object $instance Instance Object.
	 *
	 * @return void
	 */
	public function form_sms_settings_content( $instance ) {
		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-sms_settings">';
			echo '<div class="wpforms-panel-content-section-title">';
				esc_html_e( 'Admin Sms Settings', 'wp-form-sms' );
			echo '</div>';
			wpforms_panel_field(
				'text',
				'settings',
				'admin_phone_no',
				$instance->form_data,
				esc_html__( 'Admin Phone No', 'wp-form-sms' ),
				array(
					'after' => '<p class="note">' .
									sprintf(
										/* translators: %s - {all_fields} Smart Tag. */
										esc_html__( ' Insert your phone number ( e.g.: +8801746894046 )', 'wp-form-sms' )
									) .
									'</p>',
				)
			);
			wpforms_panel_field(
				'textarea',
				'sms-settings',
				'message',
				$instance->form_data,
				esc_html__( 'Message', 'wp-form-sms' ),
				array(
					'rows'       => 6,
					'default'    => '{all_fields}',
					'smarttags'  => array(
						'type' => 'all',
					),
					'parent'     => 'settings',
					'subsection' => '2',
					'class'      => 'email-msg',
					'after'      => '<p class="note">' .
									sprintf(
										/* translators: %s - {all_fields} Smart Tag. */
										esc_html__( 'To display all form fields, use the %s Smart Tag.', 'wp-form-sms' ),
										'<code>{all_fields}</code>'
									) .
									'</p>',
				)
			);
			echo '</div>';
	}

	/**
	 *  Send sms using wpforms
	 *
	 * @param  array $fields Instance Object.
	 * @param array $entry Entry Array.
	 * @param array $form_data Fromdata Array.
	 * @param int   $entry_id EntryId Int.
	 *
	 * @return $gateway
	 */
	public function send_sms( $fields, $entry, $form_data, $entry_id ) {
		$options = get_option( 'wpforms_sms_settings' );

		if ( empty( $options['sms_gateway'] ) ) {
			return new WP_Error( 'no-options', __( 'Please set your settings first', 'wp-form-sms' ), [ 'status' => 401 ] );
		}

		$admin_phone = $form_data['settings']['admin_phone_no'];
		$body        = $form_data['settings']['sms-settings'] [2] ['message'];
		$body        = apply_filters( 'wpforms_process_smart_tags', $body, $form_data, $fields, $entry_id );

		$form_data = [
			'number' => ! empty( $admin_phone ) ? $admin_phone : '',
			'body'   => $body,
		];

		$sms_gateway   = $options['sms_gateway'];
		$classname     = wpforms_sms_class_mapping( $sms_gateway );
		$gateway_class = new $classname();
		$gateway       = $gateway_class->send( $form_data, $options );

		if ( is_wp_error( $gateway ) ) {
			return $gateway->get_error_message();
		}

	}
}
