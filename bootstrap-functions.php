<?php
/**
 * Defines plugin-specific getters and functions.
 *
 * @since   1.0.0
 * @version 1.1.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WC-Plugins\LockedPaymentMethods
 *
 * @noinspection PhpMissingReturnTypeInspection
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the whitelabel name of the plugin.
 *
 * @since   1.0.0
 * @version 1.1.0
 *
 * @return  string
 */
function dws_lpmwc_name() {
	return defined( 'DWS_LPMWC_NAME' )
		? DWS_LPMWC_NAME : 'Locked Payment Methods for WooCommerce';
}

/**
 * Returns the version of the plugin.
 *
 * @since   1.0.0
 * @version 1.1.0
 *
 * @return  string|null
 */
function dws_lpmwc_version() {
	return defined( 'DWS_LPMWC_VERSION' )
		? DWS_LPMWC_VERSION : null;
}

/**
 * Returns the path to the plugin's main file.
 *
 * @since   1.0.0
 * @version 1.1.0
 *
 * @return  string|null
 */
function dws_lpmwc_path() {
	return defined( 'DWS_LPMWC_PATH' )
		? DWS_LPMWC_PATH : null;
}

/**
 * Returns the minimum PHP version required to run the plugin.
 *
 * @since   1.0.0
 * @version 1.1.0
 *
 * @return  string|null
 */
function dws_lpmwc_min_php() {
	return defined( 'DWS_LPMWC_MIN_PHP' )
		? DWS_LPMWC_MIN_PHP : null;
}

/**
 * Returns the minimum WP version required to run the plugin.
 *
 * @since   1.0.0
 * @version 1.1.0
 *
 * @return  string|null
 */
function dws_lpmwc_min_wp() {
	return defined( 'DWS_LPMWC_MIN_WP' )
		? DWS_LPMWC_MIN_WP : null;
}
