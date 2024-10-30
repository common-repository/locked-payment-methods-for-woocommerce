<?php

namespace DeepWebSolutions\WC_Plugins\LockedPaymentMethods;

use  DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Integrations\IntegrationsUnlockStrategies ;
use  DeepWebSolutions\WC_Plugins\LockedPaymentMethods\UnlockStrategies\OrderMetaStrategy ;
use  DeepWebSolutions\WC_Plugins\LockedPaymentMethods\UnlockStrategies\UserMetaStrategy ;
use  DeepWebSolutions\WC_Plugins\LockedPaymentMethods\UnlockStrategies\UserRoleStrategy ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Core\AbstractPluginFunctionality ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Hooks\Actions\SetupHooksTrait ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Hooks\HooksService ;
\defined( 'ABSPATH' ) || exit;
/**
 * Collection of permissions used by the plugin.
 *
 * @since   1.0.0
 * @version 1.2.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
class LockManager extends AbstractPluginFunctionality
{
    // region TRAITS
    use  SetupHooksTrait ;
    // endregion
    // region INHERITED METHODS
    /**
     * {@inheritDoc}
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    protected function get_di_container_children() : array
    {
        $strategies = array( UserMetaStrategy::class, UserRoleStrategy::class );
        return $strategies;
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    public function register_hooks( HooksService $hooks_service ) : void
    {
        $hooks_service->add_filter(
            'woocommerce_available_payment_gateways',
            $this,
            'remove_locked_payment_methods',
            999
        );
    }
    
    // endregion
    // region HOOKS
    /**
     * Removes payment methods from the list of available ones based on the plugin's settings.
     *
     * @since   1.0.0
     * @version 1.2.0
     *
     * @param   array   $gateways   Currently available payment methods.
     *
     * @return  array
     */
    public function remove_locked_payment_methods( array $gateways ) : array
    {
        $locked_methods_ids = dws_lpmwc_get_validated_setting( 'locked-payment-methods', 'general' );
        /**
         * Filters which payment methods are locked by default.
         *
         * @since   1.0.0
         * @version 1.0.0
         */
        $locked_methods_ids = \apply_filters( $this->get_hook_tag( 'locked_payment_methods' ), $locked_methods_ids, $gateways );
        foreach ( $locked_methods_ids as $locked_method_id ) {
            /**
             * Filters whether a given payment method is locked or not.
             *
             * @since   1.0.0
             * @version 1.0.0
             */
            $is_locked = \apply_filters( $this->get_hook_tag( 'is_locked_payment_method' ), true, $locked_method_id );
            /**
             * Filters whether a given payment method is locked or not.
             *
             * @since   1.0.0
             * @version 1.0.0
             */
            $is_locked = \apply_filters( $this->get_hook_tag( 'is_locked_payment_method', array( $locked_method_id ) ), $is_locked );
            if ( true === $is_locked ) {
                unset( $gateways[$locked_method_id] );
            }
        }
        return $gateways;
    }

}