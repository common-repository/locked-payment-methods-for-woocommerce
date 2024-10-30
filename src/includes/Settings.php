<?php

namespace DeepWebSolutions\WC_Plugins\LockedPaymentMethods;

use  DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Integrations\IntegrationsSettings ;
use  DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Settings\GeneralSettings ;
use  DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Settings\PluginSettings ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Helpers\DataTypes\Arrays ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\WooCommerce\Settings\Functionalities\WC_AbstractValidatedOptionsSectionFunctionality ;
\defined( 'ABSPATH' ) || exit;
/**
 * Registers the plugin's settings with WC.
 *
 * @since   1.0.0
 * @version 1.2.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
class Settings extends WC_AbstractValidatedOptionsSectionFunctionality
{
    // region INHERITED METHODS
    /**
     * {@inheritDoc}
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    protected function get_di_container_children() : array
    {
        $children = array( GeneralSettings::class, PluginSettings::class );
        return $children;
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.2.0
     * @version 1.2.0
     */
    public function get_options_name_prefix() : string
    {
        return 'dws-wc-lpm_';
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.2.0
     * @version 1.2.0
     */
    public function get_page_slug() : string
    {
        return 'dws-locked-payment-methods';
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.2.0
     * @version 1.2.0
     */
    public function get_page_title() : string
    {
        return \_x( 'Locked Payment Methods', 'settings', 'locked-payment-methods-for-woocommerce' );
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.2.0
     * @version 1.2.0
     */
    public function get_tab_slug() : string
    {
        return 'checkout';
    }

}