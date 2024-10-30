<?php

namespace DeepWebSolutions\WC_Plugins\LockedPaymentMethods\UnlockStrategies;

use DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Settings\GeneralSettings;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Core\AbstractPluginFunctionality;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Foundations\Actions\Initializable\Integrations\SetupableInactiveTrait;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Foundations\States\Activeable\ActiveLocalTrait;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Hooks\Actions\SetupHooksTrait;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Validation\Actions\InitializeValidationServiceTrait;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Validation\ValidationTypesEnum;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\WooCommerce\Settings\Functionalities\WC_AbstractValidatedOptionsGroupFunctionality;

\defined( 'ABSPATH' ) || exit;

/**
 * Template to encapsulate the most often needed functionalities of a payment method unlocking strategy.
 *
 * @since   1.0.0
 * @version 1.3.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
abstract class AbstractUnlockStrategy extends AbstractPluginFunctionality {
	// region TRAITS

	use ActiveLocalTrait;
	use InitializeValidationServiceTrait;
	use SetupableInactiveTrait;
	use SetupHooksTrait;

	// endregion

	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.3.0
	 */
	public function is_active_local(): bool {
		$enabled_key = $this->get_enabled_option_id();
		$raw_value   = dws_lpmwc_get_raw_setting( $enabled_key, 'general' );

		return $this->validate_option_value( $raw_value, $enabled_key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.3.0
	 */
	public function register_hooks( HooksService $hooks_service ): void {
		$options_group = $this->get_options_group_instance();
		$hooks_service->add_filter( $options_group->get_hook_tag( 'get_group_fields' ), $this, 'register_options_fields', 10, 1, 'direct' );
		$hooks_service->add_filter( $options_group->get_hook_tag( 'validate_option_value' ), $this, 'validate_option_value', 10, 2, 'direct' );

		if ( $this->is_active() ) {
			$hooks_service->add_filter( dws_lpmwc_get_component_hook_tag( 'lock-manager', 'is_locked_payment_method' ), $this, 'maybe_unlock_payment_method', 10, 2 );
			$this->register_hooks_active( $hooks_service );
		}
	}

	// endregion

	// region METHODS

	/**
	 * Returns the strategy's name.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	abstract public function get_strategy_name(): string;

	/**
	 * Returns the description of the strategy that will be displayed on the options page.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	abstract public function get_strategy_description(): string;

	// endregion

	// region HOOKS

	/**
	 * Registers the strategy's options within the general options group.
	 *
	 * @since   1.0.0
	 * @version 1.2.0
	 *
	 * @param   array   $options    Currently registered general options.
	 *
	 * @return  array
	 */
	public function register_options_fields( array $options ): array {
		return Arrays::insert_after( $options, 'locked-payment-methods', $this->get_options_fields() );
	}

	/**
	 * Validates the strategy's options.
	 *
	 * @since   1.0.0
	 * @version 1.2.0
	 *
	 * @param   mixed   $value      The current value of the field.
	 * @param   string  $field_id   The ID of the option currently being validated.
	 *
	 * @return  mixed
	 */
	public function validate_option_value( $value, string $field_id ) {
		$enabled_key = $this->get_enabled_option_id();

		switch ( $field_id ) {
			case $enabled_key:
				$value = $this->validate_value( $value, $this->get_options_group_instance()->generate_validation_key( $enabled_key ), ValidationTypesEnum::BOOLEAN );
				break;
			default:
				$value = $this->validate_option_value_active( $value, $field_id );
		}

		return $value;
	}

	/**
	 * Maybe unlocks access to a given payment method based on the current strategy.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   bool        $is_locked              Whether the payment method is still locked.
	 * @param   string      $locked_method_id       The gateway ID.
	 *
	 * @return  bool
	 */
	public function maybe_unlock_payment_method( bool $is_locked, string $locked_method_id ): bool {
		if ( ! $this->is_disabled() && $this->is_active() ) {
			$is_locked = $this->check_payment_method_access( ...\func_get_args() );
		}

		return $is_locked;
	}

	// endregion

	// region HELPERS

	/**
	 * Registers actions and filters with the hooks service ONLY IF the unlock strategy is active.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   HooksService    $hooks_service      Instance of the hooks service.
	 */
	protected function register_hooks_active( HooksService $hooks_service ): void {
		/* empty on purpose */
	}

	/**
	 * Determines whether access to a given payment method is locked or not based on current strategy.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool        $is_locked              Whether the payment method is still locked.
	 * @param   string      $locked_method_id       The gateway ID.
	 *
	 * @return  bool
	 */
	abstract protected function check_payment_method_access( bool $is_locked, string $locked_method_id ): bool;

	/**
	 * Returns the ID of the option which determines whether the strategy is enabled or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	protected function get_enabled_option_id(): string {
		return 'unlock-via-' . $this->get_id();
	}

	/**
	 * Returns the definition for settings that should be registered on the WC settings page.
	 *
	 * @since   1.0.0
	 * @version 1.2.0
	 *
	 * @return  array[]
	 */
	protected function get_options_fields(): array {
		$enabled_key = $this->get_enabled_option_id();

		$settings = array(
			$enabled_key => array(
				'title'    => \wp_sprintf(
					/* translators: %s: strategy name. */
					\__( 'Unlock via %s', 'locked-payment-methods-for-woocommerce' ),
					$this->get_strategy_name()
				),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'default'  => $this->get_default_value( $this->get_options_group_instance()->generate_validation_key( $enabled_key ) ),
				'options'  => $this->get_supported_options( 'boolean' ),
				'desc_tip' => $this->get_strategy_description(),
			),
		);

		if ( $this->is_active_local() ) {
			$settings += $this->get_options_fields_active();
		}

		return $settings;
	}

	/**
	 * Returns the definition for settings that should be registered on the WC settings page ONLY IF the strategy is active.
	 *
	 * @since   1.0.0
	 * @version 1.2.0
	 *
	 * @return  array[]
	 */
	protected function get_options_fields_active(): array {
		return array();
	}

	/**
	 * Validates the strategy's active options.
	 *
	 * @since   1.0.0
	 * @version 1.2.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   mixed   $value      The current value of the field.
	 * @param   string  $field_id   The ID of the option currently being validated.
	 *
	 * @return  mixed
	 */
	protected function validate_option_value_active( $value, string $field_id ) {
		return $value;
	}

	/**
	 * Returns the instance of the groups object instance to register the options with.
	 *
	 * @since   1.0.0
	 * @version 1.3.0
	 *
	 * @return  WC_AbstractValidatedOptionsGroupFunctionality
	 */
	protected function get_options_group_instance(): WC_AbstractValidatedOptionsGroupFunctionality {
		return $this->get_plugin()->get_container_entry( GeneralSettings::class );
	}

	// endregion
}
