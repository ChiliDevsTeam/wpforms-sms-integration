<?php
/**
 * FormSettings  class
 *
 * Manage  FormSettings related functionality on Wp Form
 *
 * @package Chilidevs\WpFormSms
 */

declare(strict_types=1);

namespace Chilidevs\WpFormSms\Admin;

use WP_Error;

/**
 * FormSettings Class.
 *
 * @package Chilidevs\WpFormSms\Admin
 */
class FormSettings {

	/**
	 * Load automatically when class initiate
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'wpforms_builder_settings_sections', [ $this, 'form_sms_settings_section' ], 20, 2 );
		add_action( 'wpforms_form_settings_panel_content', [ $this, 'form_sms_settings_content' ],10, 1 );
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
			);
			wpforms_panel_field(
				'textarea',
				'settings',
				'message',
				$instance->form_data,
				esc_html__( 'Message', 'wp-form-sms' ),
			);
			echo '</div>';
	}
}
