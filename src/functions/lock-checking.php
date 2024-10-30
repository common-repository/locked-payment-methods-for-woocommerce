<?php

defined( 'ABSPATH' ) || exit;
/**
 * Checks whether a payment method is locked or not for a given user based on their meta values.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param   string      $locked_method_id       Payment method to check access for.
 * @param   int         $user_id                The ID of the user to check.
 *
 * @return  bool    True if it's locked, false if it's available.
 */
function dws_lpmwc_check_payment_method_access_via_user_meta( string $locked_method_id, int $user_id ) : bool
{
    $user_meta_checker = dws_lpmwc_component( 'user-meta-strategy' );
    return $user_meta_checker->maybe_unlock_payment_method( true, $locked_method_id, $user_id );
}

/**
 * Checks whether a payment method is locked or not for a given user based on their user roles.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param   string      $locked_method_id       Payment method to check access for.
 * @param   int         $user_id                The ID of the user to check.
 *
 * @return  bool    True if it's locked, false if it's available.
 */
function dws_lpmwc_check_payment_method_access_via_user_roles( string $locked_method_id, int $user_id ) : bool
{
    $user_role_checker = dws_lpmwc_component( 'user-role-strategy' );
    return $user_role_checker->maybe_unlock_payment_method( true, $locked_method_id, $user_id );
}

/**
 * Checks whether a payment method is locked or not for a given user based on all applicable strategies.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param   string      $locked_method_id       Payment method to check access for.
 * @param   int         $user_id                The ID of the user to check.
 *
 * @return  bool    True if it's locked, false if it's available.
 */
function dws_lpmwc_check_payment_method_access_for_user( string $locked_method_id, int $user_id ) : bool
{
    $is_locked = dws_lpmwc_check_payment_method_access_via_user_roles( $locked_method_id, $user_id );
    $is_locked = $is_locked && dws_lpmwc_check_payment_method_access_via_user_meta( $locked_method_id, $user_id );
    /**
     * Filters whether a given payment method is locked or not for the given user.
     *
     * @since   1.0.0
     * @version 1.0.0
     */
    return apply_filters(
        dws_lpmwc_get_hook_tag( 'check_payment_method_access_for_user' ),
        $is_locked,
        $locked_method_id,
        $user_id
    );
}
