<?php

namespace DeepWebSolutions\WC_Plugins\LockedPaymentMethods;

use  DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Integrations\IntegrationsPermissions ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Core\Functionalities\AbstractPermissionsFunctionality ;
\defined( 'ABSPATH' ) || exit;
/**
 * Collection of permissions used by the plugin.
 *
 * @since   1.0.0
 * @version 1.2.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
class Permissions extends AbstractPermissionsFunctionality
{
    // region PERMISSION CONSTANTS
    /**
     * Permission required to be able to approve a user's account for using locked payment methods.
     *
     * @since   1.0.0
     * @version 1.1.0
     *
     * @var     string
     */
    public const  UNLOCK_PAYMENT_METHODS_USERS = 'unlock_dws_lpm_via_user_profiles' ;
    /**
     * Permission required to be able to approve a specific order for being paid with a locked payment method.
     *
     * @since   1.0.0
     * @version 1.1.0
     *
     * @var     string
     */
    public const  UNLOCK_PAYMENT_METHODS_ORDERS = 'unlock_dws_lpm_via_orders' ;
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
        $permissions = array();
        return $permissions;
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.0.0
     * @version 1.1.0
     */
    public function get_granting_rules() : array
    {
        return array(
            'administrator' => 'all',
            'shop_manager'  => 'all',
        );
    }

}