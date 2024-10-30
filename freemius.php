<?php

defined( 'ABSPATH' ) || exit;
/**
 * Returns the Freemius instance of the current plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @noinspection PhpDocMissingThrowsInspection
 *
 * @return  Freemius
 */
function dws_lpmwc_fs() : Freemius
{
    global  $dws_lpmwc_fs ;
    
    if ( !isset( $dws_lpmwc_fs ) ) {
        // Activate multisite network integration.
        if ( !defined( 'WP_FS__PRODUCT_8062_MULTISITE' ) ) {
            define( 'WP_FS__PRODUCT_8062_MULTISITE', true );
        }
        // Include Freemius SDK.
        require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
        /* @noinspection PhpUnhandledExceptionInspection */
        $dws_lpmwc_fs = fs_dynamic_init( array(
            'id'             => '8062',
            'slug'           => 'locked-payment-methods-for-woocommerce',
            'type'           => 'plugin',
            'public_key'     => 'pk_8c3cd162eb6934adc6bc2917dd461',
            'is_premium'     => false,
            'premium_suffix' => 'Premium',
            'has_addons'     => false,
            'has_paid_plans' => true,
            'menu'           => array(
            'first-path' => 'plugins.php',
        ),
            'is_live'        => true,
        ) );
    }
    
    return $dws_lpmwc_fs;
}

/**
 * Initializes the Freemius global instance and sets a few defaults.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  Freemius
 */
function dws_lpmwc_fs_init() : Freemius
{
    $freemius = dws_lpmwc_fs();
    /**
     * Triggered after the Freemius SDK was initialized.
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    do_action( 'dws_lpmwc_fs_loaded' );
    $freemius->add_filter( 'after_skip_url', 'dws_lpmwc_fs_settings_url' );
    $freemius->add_filter( 'after_connect_url', 'dws_lpmwc_fs_settings_url' );
    $freemius->add_filter( 'after_pending_connect_url', 'dws_lpmwc_fs_settings_url' );
    return $freemius;
}

/**
 * Returns the URL to the settings page.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function dws_lpmwc_fs_settings_url() : string
{
    return ( dws_lpmwc_instance()->is_active() ? admin_url( 'admin.php?page=wc-settings&tab=checkout&section=dws-locked-payment-methods' ) : admin_url( 'plugins.php' ) );
}
