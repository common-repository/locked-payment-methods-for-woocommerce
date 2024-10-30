<?php

use function  DWS_LPMWC_Deps\DI\factory ;
defined( 'ABSPATH' ) || exit;
$settings = array(
    'defaults' => array(
    'general' => array(
    'locked-payment-methods' => array(),
    'unlock-via-user-role'   => 'yes',
    'full-access-user-roles' => array( 'administrator', 'shop_manager' ),
    'unlock-via-user-meta'   => 'yes',
),
    'plugin'  => array(
    'remove-data-uninstall' => 'no',
),
),
    'options'  => array(
    'boolean' => array(
    'yes' => factory( function () {
    return _x( 'Yes', 'settings', 'locked-payment-methods-for-woocommerce' );
} ),
    'no'  => factory( function () {
    return _x( 'No', 'settings', 'locked-payment-methods-for-woocommerce' );
} ),
),
    'general' => array(
    'full-access-user-roles' => factory( function () {
    return array_combine( array_keys( wp_roles()->roles ), array_map( function ( string $name ) {
        return translate_user_role( $name );
    }, array_column( wp_roles()->roles, 'name' ) ) );
} ),
),
),
);
return $settings;