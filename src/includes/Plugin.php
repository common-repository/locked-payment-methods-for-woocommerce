<?php

namespace DeepWebSolutions\WC_Plugins\LockedPaymentMethods;

use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Core\AbstractPluginFunctionalityRoot ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Core\Actions\Installable\UninstallFailureException ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Dependencies\Actions\InitializePluginDependenciesContextHandlersTrait ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Dependencies\Actions\SetupActiveStateDependenciesAdminNoticesTrait ;
use  DWS_LPMWC_Deps\DeepWebSolutions\Framework\WooCommerce\WC_Helpers ;
\defined( 'ABSPATH' ) || exit;
/**
 * Main plugin class.
 *
 * @since   1.0.0
 * @version 1.3.2
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
final class Plugin extends AbstractPluginFunctionalityRoot
{
    // region TRAITS
    use  InitializePluginDependenciesContextHandlersTrait ;
    use  SetupActiveStateDependenciesAdminNoticesTrait ;
    // endregion
    // region INHERITED METHODS
    /**
     * Returns the WC plugin-level dependency.
     *
     * @since   1.2.0
     * @version 1.3.0
     *
     * @return  array
     */
    protected function get_plugin_dependencies_active() : array
    {
        return array(
            'plugin'         => 'woocommerce/woocommerce.php',
            'fallback_name'  => 'WooCommerce',
            'min_version'    => '4.5.2',
            'version_getter' => array( WC_Helpers::class, 'get_version' ),
        );
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    protected function get_di_container_children() : array
    {
        return \array_merge( parent::get_di_container_children(), array( Settings::class, Permissions::class, LockManager::class ) );
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.3.2
     * @version 1.3.2
     */
    public function initialize() : ?InitializationFailureException
    {
        // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
        // The parent class method is overwritten by the plugin dependencies trait in this class, so we need this override to bypass that.
        return parent::initialize();
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.2.0
     * @version 1.2.0
     */
    public function uninstall() : ?UninstallFailureException
    {
        if ( true === dws_lpmwc_get_validated_setting( 'remove-data-uninstall', 'plugin' ) ) {
            return parent::uninstall();
        }
        return null;
    }
    
    // endregion
    // region HOOKS
    /**
     * {@inheritDoc}
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    public function register_plugin_actions(
        array $actions,
        string $plugin_file,
        array $plugin_data,
        string $context
    ) : array
    {
        $action_links = array();
        if ( $this->is_active() ) {
            $action_links['settings'] = '<a href="' . dws_lpmwc_fs_settings_url() . '" aria-label="' . \esc_attr__( 'View settings', 'locked-payment-methods-for-woocommerce' ) . '">' . \esc_html__( 'Settings', 'locked-payment-methods-for-woocommerce' ) . '</a>';
        }
        if ( !dws_lpmwc_fs()->is_premium() || !(dws_lpmwc_fs()->is_activation_mode() || dws_lpmwc_fs()->can_use_premium_code()) ) {
            $action_links['upgrade'] = '<a href="' . \esc_url( dws_lpmwc_fs()->get_upgrade_url() ) . '" aria-label="' . \esc_attr__( 'Upgrade for premium features', 'locked-payment-methods-for-woocommerce' ) . '">' . \esc_html__( 'Upgrade', 'locked-payment-methods-for-woocommerce' ) . '</a>';
        }
        return \array_merge( $action_links, $actions );
    }
    
    /**
     * {@inheritDoc}
     *
     * @since   1.0.0
     * @version 1.3.1
     */
    public function register_plugin_row_meta(
        array $plugin_meta,
        string $plugin_file,
        array $plugin_data,
        string $status
    ) : array
    {
        if ( $this->get_plugin_basename() !== $plugin_file ) {
            return $plugin_meta;
        }
        $row_meta = array(
            'support' => '<a href="' . \esc_url( dws_lpmwc_fs()->get_support_forum_url() ) . '" aria-label="' . \esc_attr__( 'Visit community forums', 'locked-payment-methods-for-woocommerce' ) . '">' . \esc_html__( 'Community support', 'locked-payment-methods-for-woocommerce' ) . '</a>',
        );
        return \array_merge( $plugin_meta, $row_meta );
    }

}