<?php

namespace DeepWebSolutions\WC_Plugins\LockedPaymentMethods\UnlockStrategies;

use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Helpers\Users;

\defined( 'ABSPATH' ) || exit;

/**
 * Unlocks payment methods based on the user's roles.
 *
 * @since   1.0.0
 * @version 1.3.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
class UserRoleStrategy extends AbstractUnlockStrategy {
	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_strategy_name(): string {
		return \_x( 'User Roles', 'unlock-strategies', 'locked-payment-methods-for-woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_strategy_description(): string {
		return \__( 'Users with certain roles will be granted full access to all the locked payment methods.', 'locked-payment-methods-for-woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.3.0
	 */
	protected function check_payment_method_access( bool $is_locked, string $locked_method_id, ?int $user_id = null ): bool {
		$roles   = dws_lpmwc_get_validated_general_setting( 'full-access-user-roles' );
		$user_id = $user_id ?? \get_current_user_id();

		return Users::has_roles( $roles, $user_id ) ? false : $is_locked;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.2.0
	 */
	protected function get_options_fields_active(): array {
		return array(
			'full-access-user-roles' => array(
				'title'   => \__( 'User roles with full access to all enabled payment methods', 'locked-payment-methods-for-woocommerce' ),
				'type'    => 'multiselect',
				'class'   => 'wc-enhanced-select',
				'default' => $this->get_default_value( $this->get_options_group_instance()->generate_validation_key( 'full-access-user-roles' ) ),
				'options' => $this->get_supported_options( $this->get_options_group_instance()->generate_validation_key( 'full-access-user-roles' ) ),
			),
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.2.0
	 */
	protected function validate_option_value_active( $value, string $field_id ) {
		switch ( $field_id ) {
			case 'full-access-user-roles':
				$key   = $this->get_options_group_instance()->generate_validation_key( $field_id );
				$value = $this->get_validation_service()->validate_allowed_array( $value, $key, $key );
				break;
		}

		return $value;
	}

	// endregion
}
