<?php
/**
 * GatewayInterface Interface
 *
 * Manage  Getway related functionality on Wp Form
 *
 * @package ChiliDevs\WpFormSms
 */

declare(strict_types=1);

namespace ChiliDevs\WpFormSms\Gateways;

use WP_Error;

/**
 * GatewayInterface Interface.
 *
 * @package ChiliDevs\WpFormSms\Gateways
 */
interface GatewayInterface {
	/**
	 * Send SMS via gateways
	 *
	 * @param array $form_data Hold form data.
	 * @param array $options Keep all gateway settings.
	 *
	 * @return array
	 */
	public function send( $form_data, $options );
}
