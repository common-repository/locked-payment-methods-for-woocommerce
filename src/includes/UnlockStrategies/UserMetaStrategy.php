<?php

namespace DeepWebSolutions\WC_Plugins\LockedPaymentMethods\UnlockStrategies;

use DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Permissions;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Helpers\DataTypes\Booleans;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Helpers\Users;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use WP_User;
use WP_User_Query;

\defined( 'ABSPATH' ) || exit;

/**
 * Unlocks payment methods based on the settings in the user's profile.
 *
 * @since   1.0.0
 * @version 1.2.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
class UserMetaStrategy extends AbstractUnlockStrategy {
	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_strategy_name(): string {
		return \_x( 'User Meta', 'unlock-strategies', 'locked-payment-methods-for-woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_strategy_description(): string {
		return \__( 'Adds controls for granting access to each locked payment method separately to each user\'s profile page. Access is granted for all future orders and all current unpaid orders.', 'locked-payment-methods-for-woocommerce' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function register_hooks_active( HooksService $hooks_service ): void {
		$hooks_service->add_filter( 'manage_users_columns', $this, 'register_users_table_columns' );
		$hooks_service->add_filter( 'manage_users_custom_column', $this, 'output_users_table_columns', 10, 3 );

		$hooks_service->add_action( 'restrict_manage_users', $this, 'output_users_table_filter' );
		$hooks_service->add_filter( 'pre_get_users', $this, 'filter_users_by_unlocked_methods' );

		if ( Users::has_capabilities( array( 'edit_users', Permissions::UNLOCK_PAYMENT_METHODS_USERS ) ) ) {
			$hooks_service->add_action( 'show_user_profile', $this, 'register_settings', 30 );
			$hooks_service->add_action( 'edit_user_profile', $this, 'register_settings', 30 );
			$hooks_service->add_action( 'personal_options_update', $this, 'save_settings' );
			$hooks_service->add_action( 'edit_user_profile_update', $this, 'save_settings' );
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function check_payment_method_access( bool $is_locked, string $locked_method_id, ?int $user_id = null ): bool {
		$user_id = $user_id ?? \get_current_user_id();
		return ( 'yes' === \get_user_meta( $user_id, "dws_wc_lpm_grant_access_$locked_method_id", true ) )
			? false : $is_locked;
	}

	// endregion

	// region HOOKS

	/**
	 * Registers a new users' table column.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $columns    Table columns registered so far.
	 *
	 * @return  array
	 */
	public function register_users_table_columns( array $columns ): array {
		return Arrays::insert_after( $columns, 'role', array( 'dws_lpm_unlocked_for' => \__( 'Unlocked for', 'locked-payment-methods-for-woocommerce' ) ) );
	}

	/**
	 * Outputs the content of the newly registered column.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $output         The content of the column currently rendered.
	 * @param   string      $column_name    The name of the column currently rendered.
	 * @param   int         $user_id        The ID of the user of the current table row.
	 *
	 * @return  string
	 */
	public function output_users_table_columns( string $output, string $column_name, int $user_id ): string {
		static $gateways = null;

		if ( 'dws_lpm_unlocked_for' === $column_name ) {
			if ( \is_null( $gateways ) ) {
				$gateways = \WC()->payment_gateways()->payment_gateways();
			}

			$output = array();

			$locked_methods_ids = dws_lpmwc_get_validated_general_setting( 'locked-payment-methods' );
			foreach ( $locked_methods_ids as $locked_method_id ) {
				$icon_html = '';

				$is_locked = $this->check_payment_method_access( true, $locked_method_id, $user_id );
				if ( false === $is_locked ) {
					$icon_html .= '<span class="dashicons dashicons-yes"></span>';
				} else {
					$is_locked  = dws_lpmwc_check_payment_method_access_for_user( $locked_method_id, $user_id );
					$icon_html .= $is_locked ? '<span class="dashicons dashicons-no-alt"></span>' : '<span class="dashicons dashicons-marker"></span>';
				}

				$output[] = "$icon_html<span>{$gateways[ $locked_method_id ]->title}</span>";
			}

			$output = \implode( '<br/>', $output );
		}

		return $output;
	}

	/**
	 * Output a multiselect filter to let users filter out customers that are unlocked for given payment methods.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $which  The location of the extra table nav markup: 'top' or 'bottom'.
	 */
	public function output_users_table_filter( string $which ) {
		$locked_methods_ids = dws_lpmwc_get_validated_general_setting( 'locked-payment-methods' );
		if ( empty( $locked_methods_ids ) ) {
			return;
		}

		$gateways        = \WC()->payment_gateways()->payment_gateways();
		$select_template = '<select name="dws_wc_lpm_filter_%s" style="float: none; margin-left: 10px;"><option value="">%s</option>%s</select>';
		$option_template = '<option value="%s" %s>%s</option>';

		$active_filter = \filter_input( INPUT_GET, 'dws_wc_lpm_filter_top', FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE )
			?? \filter_input( INPUT_GET, 'dws_wc_lpm_filter_bottom', FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE ) ?? '';

		$options = \implode(
			'',
			\array_map(
				function( string $locked_method_id ) use ( $option_template, $active_filter, $gateways ) {
					return \wp_sprintf( $option_template, $locked_method_id, \selected( $active_filter, $locked_method_id, false ), $gateways[ $locked_method_id ]->title );
				},
				$locked_methods_ids
			)
		);
		$select  = \wp_sprintf( $select_template, $which, \esc_html__( 'Unlocked for...', 'locked-payment-methods-for-woocommerce' ), $options );

		echo $select; // phpcs:ignore WordPress.Security.EscapeOutput
		\submit_button( \esc_html_x( 'Filter', 'users table', 'locked-payment-methods-for-woocommerce' ), null, $which, false );
	}

	/**
	 * Maybe include additional meta filtering to the user query.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   WP_User_Query   $query      The current WP user query object.
	 */
	public function filter_users_by_unlocked_methods( WP_User_Query $query ) {
		global $pagenow;

		if ( \is_admin() && 'users.php' === $pagenow ) {
			$active_filter = \filter_input( INPUT_GET, 'dws_wc_lpm_filter_top', FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE )
				?? \filter_input( INPUT_GET, 'dws_wc_lpm_filter_bottom', FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE ) ?? '';

			if ( ! empty( $active_filter ) ) {
				$meta_query = $query->get( 'meta_query' );
				$meta_query = $meta_query ?? array();

				$meta_query[] = array(
					'key'   => "dws_wc_lpm_grant_access_$active_filter",
					'value' => 'yes',
				);
				$query->set( 'meta_query', $meta_query );
			}
		}
	}

	/**
	 * Outputs HTML checkboxes to a user's back-end profile which if enabled activate blocked payment methods for the
	 * respective account.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   WP_User     $user       The user whose profile is currently being rendered.
	 */
	public function register_settings( WP_User $user ) {
		$locked_methods_ids = dws_lpmwc_get_validated_general_setting( 'locked-payment-methods' );
		if ( empty( $locked_methods_ids ) ) {
			return;
		}

		$gateways = WC()->payment_gateways()->payment_gateways(); ?>

		<h2>
			<?php \esc_html_e( 'Locked Payment Methods for WooCommerce', 'locked-payment-methods-for-woocommerce' ); ?>
		</h2>
		<table class="form-table" id="dws-wc-locked-payment-methods">
			<tbody>
			<?php
			foreach ( $locked_methods_ids as $locked_method_id ) :
				$field_id    = "dws_wc_lpm_grant_access_$locked_method_id";
				$field_value = \get_user_meta( $user->ID, $field_id, true );
				?>
				<tr>
					<th>
						<?php echo \esc_html( $gateways[ $locked_method_id ]->title ); ?>
					</th>
					<td>
						<label for="<?php echo \esc_attr( $field_id ); ?>">
							<input name="<?php echo \esc_attr( $field_id ); ?>" type="checkbox" id="<?php echo \esc_attr( $field_id ); ?>" value="1" <?php \checked( $field_value, 'yes' ); ?>/>
							<?php
							echo \wp_kses_post(
								\wp_sprintf(
									/* translators: Name of the payment gateway. */
									\__( 'Unlock the <strong>%s</strong> payment method for this user?', 'locked-payment-methods-for-woocommerce' ),
									$gateways[ $locked_method_id ]->title
								)
							);
							?>
							<?php if ( 'yes' !== $field_value && ! dws_lpmwc_check_payment_method_access_for_user( $locked_method_id, $user->ID ) ) : ?>
							<p class="description">
								<?php \esc_html_e( 'The customer is already granted access to this payment method through other means.', 'locked-payment-methods-for-woocommerce' ); ?>
							</p>
							<?php endif; ?>
						</label>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php
	}

	/**
	 * Saves the value of the checkboxes which unblock payment methods for a user to the user's meta.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $user_id    The ID of the user whose profile was just saved.
	 */
	public function save_settings( int $user_id ) {
		$locked_methods_ids = dws_lpmwc_get_validated_general_setting( 'locked-payment-methods' );
		foreach ( $locked_methods_ids as $locked_method_id ) {
			$key   = "dws_wc_lpm_grant_access_$locked_method_id";
			$value = Booleans::maybe_cast_input( INPUT_POST, $key, false );

			( true === $value )
				? \update_user_meta( $user_id, $key, 'yes' )
				: \delete_user_meta( $user_id, $key );
		}
	}

	// endregion
}
