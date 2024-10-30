<?php

namespace DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Settings;

use DWS_LPMWC_Deps\DeepWebSolutions\Framework\WooCommerce\Settings\Functionalities\WC_AbstractValidatedOptionsGroupFunctionality;

\defined( 'ABSPATH' ) || exit;

/**
 * Registers the plugin's General Settings with WC.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
class GeneralSettings extends WC_AbstractValidatedOptionsGroupFunctionality {
	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_group_title(): string {
		return \__( 'General Settings', 'locked-payment-methods-for-woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 */
	protected function get_group_fields_helper(): array {
		$enabled_gateways = \array_filter(
			\WC()->payment_gateways()->payment_gateways(),
			function( \WC_Payment_Gateway $gateway ) {
				return 'yes' === $gateway->enabled;
			}
		);

		return array(
			'locked-payment-methods' => array(
				'title'    => \__( 'Payment methods which are locked by default', 'locked-payment-methods-for-woocommerce' ),
				'type'     => 'multiselect',
				'class'    => 'wc-enhanced-select',
				'default'  => $this->get_default_value( 'locked-payment-methods' ),
				'options'  => \array_combine(
					\array_column( $enabled_gateways, 'id' ),
					\array_column( $enabled_gateways, 'title' )
				),
				'desc_tip' => \__( 'Only enabled payment methods can be selected', 'locked-payment-methods-for-woocommerce' ),
			),
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.2.0
	 * @version 1.2.0
	 */
	protected function validate_option_value_helper( $value, string $field_id ) {
		switch ( $field_id ) {
			case 'locked-payment-methods':
				$value = \array_filter( (array) $value, 'is_string' );
				break;
		}

		return $value;
	}

	// endregion
}
