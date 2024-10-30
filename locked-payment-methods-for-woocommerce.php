<?php
/**
 * The Locked Payment Methods for WooCommerce bootstrap file.
 *
 * @since               1.0.0
 * @version             1.1.0
 * @package             DeepWebSolutions\WC-Plugins\LockedPaymentMethods
 * @author              Deep Web Solutions
 * @copyright           2021 Deep Web Solutions
 * @license             GPL-3.0-or-later
 *
 * @noinspection        ALL
   *
 * @wordpress-plugin
 * Plugin Name:             Locked Payment Methods for WooCommerce
 * Plugin URI:              https://www.deep-web-solutions.com/plugins/locked-payment-methods-for-woocommerce/
 * Description:             A WooCommerce extension which allows shop managers to hide payment methods from customers that haven't been manually granted access yet.
 * Version:                 1.3.5
 * Requires at least:       5.5
 * Requires PHP:            7.4
 * Author:                  Deep Web Solutions
 * Author URI:              https://www.deep-web-solutions.com
 * License:                 GPL-3.0+
 * License URI:             http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:             locked-payment-methods-for-woocommerce
 * Domain Path:             /src/languages
 * WC requires at least:    4.5.2
 * WC tested up to:         6.7
 */

defined( 'ABSPATH' ) || exit;

if ( function_exists( 'dws_lpmwc_fs' ) ) {
	dws_lpmwc_fs()->set_basename( false, __FILE__ );
	return;
}

// Start by autoloading dependencies and defining a few functions for running the bootstrapper.
is_file( __DIR__ . '/vendor/autoload.php' ) && require_once __DIR__ . '/vendor/autoload.php';

// Load plugin-specific bootstrapping functions.
require_once __DIR__ . '/bootstrap-functions.php';

// Check that the DWS WP Framework is loaded.
if ( ! function_exists( '\DWS_LPMWC_Deps\DeepWebSolutions\Framework\dws_wp_framework_get_bootstrapper_init_status' ) ) {
	add_action(
		'admin_notices',
		function() {
			$message      = wp_sprintf( /* translators: %s: Plugin name. */ __( 'It seems like <strong>%s</strong> is corrupted. Please reinstall!', 'locked-payment-methods-for-woocommerce' ), dws_lpmwc_name() );
			$html_message = wp_sprintf( '<div class="error notice dws-plugin-corrupted-error">%s</div>', wpautop( $message ) );
			echo wp_kses_post( $html_message );
		}
	);
	return;
}

// Define plugin constants.
define( 'DWS_LPMWC_NAME', DWS_LPMWC_Deps\DeepWebSolutions\Framework\dws_wp_framework_get_whitelabel_name() . ': Locked Payment Methods for WooCommerce' );
define( 'DWS_LPMWC_VERSION', '1.3.5' );
define( 'DWS_LPMWC_PATH', __FILE__ );

// Define minimum environment requirements.
define( 'DWS_LPMWC_MIN_PHP', '7.4' );
define( 'DWS_LPMWC_MIN_WP', '5.5' );

// Start plugin initialization if system requirements check out.
if ( DWS_LPMWC_Deps\DeepWebSolutions\Framework\dws_wp_framework_check_php_wp_requirements_met( dws_lpmwc_min_php(), dws_lpmwc_min_wp() ) ) {
	if ( ! function_exists( 'dws_lpmwc_fs' ) ) {
		include __DIR__ . '/freemius.php';
		dws_lpmwc_fs_init();
	}

	include __DIR__ . '/functions.php';

	add_action( 'plugins_loaded', 'dws_lpmwc_instance_initialize' );
	register_activation_hook( __FILE__, 'dws_lpmwc_plugin_activate' );
} else {
	DWS_LPMWC_Deps\DeepWebSolutions\Framework\dws_wp_framework_output_requirements_error( dws_lpmwc_name(), dws_lpmwc_version(), dws_lpmwc_min_php(), dws_lpmwc_min_wp() );
}
