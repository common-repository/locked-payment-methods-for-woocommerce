<?php

use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Helpers\DataTypes\Strings;

defined( 'ABSPATH' ) || exit;

/**
 * Returns the raw database value of a plugin's option.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param   string          $field_id   The ID of the option field to retrieve.
 * @param   string|null     $group      The group to retrieve the setting from.
 *
 * @return  mixed|null
 */
function dws_lpmwc_get_raw_setting( string $field_id, ?string $group = null ) {
	$group = is_null( $group ) ? 'settings' : Strings::maybe_suffix( $group, '-settings' );
	return dws_lpmwc_component( $group )->get_option_value( $field_id );
}

/**
 * Returns the validated database value of a plugin's option.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param   string          $field_id   The ID of the option field to retrieve.
 * @param   string|null     $group      The group to retrieve the setting from.
 *
 * @return  mixed|null
 */
function dws_lpmwc_get_validated_setting( string $field_id, ?string $group = null ) {
	$group = is_null( $group ) ? 'settings' : Strings::maybe_suffix( $group, '-settings' );
	return dws_lpmwc_component( $group )->get_validated_option_value( $field_id );
}

/**
 * Returns the given value validated against the routine of the given field.
 *
 * @since   1.3.0
 * @version 1.3.0
 *
 * @param   mixed           $value          Value to validate.
 * @param   string          $field_id       The ID of the field that the value belongs to.
 * @param   string|null     $group          The group that the field belongs to.
 *
 * @return  mixed
 */
function dws_lpmwc_validate_setting( $value, string $field_id, ?string $group = null ) {
	$group = is_null( $group ) ? 'settings' : Strings::maybe_suffix( $group, '-settings' );
	return dws_lpmwc_component( $group )->validate_option_value( $value, $field_id );
}

/**
 * Returns the validated database value of a plugin's genral option.
 *
 * @since   1.0.0
 * @version 1.2.0
 *
 * @param   string  $field_id   ID of the option field to retrieve.
 *
 * @return  mixed|null
 */
function dws_lpmwc_get_validated_general_setting( string $field_id ) {
	return dws_lpmwc_get_validated_setting( $field_id, 'general' );
}
