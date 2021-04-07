<?php
/**
 * Vonage class
 *
 * Manage  Vonage related functionality on Wp Form
 *
 * @package Chilidevs\WpFormSms
 */

declare(strict_types=1);

namespace Chilidevs\WpFormSms\Gateways;

use WP_Error;

/**
 *  Vonage Class.
 *
 * @package Chilidevs\WpFormSms\Gateways
 */
class Vonage implements GatewayInterface {
	/**
	 * Send SMS via gateways
	 *
	 * @param array $form_data Hold form data.
	 * @param array $options Keep all gateway settings.
	 *
	 * @return void
	 */
	public function send( $form_data, $options ) {

	}
}
