<?php

use DeepWebSolutions\WC_Plugins\LockedPaymentMethods\LockManager;
use DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Permissions;
use DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Settings;
use DeepWebSolutions\WC_Plugins\LockedPaymentMethods\UnlockStrategies;
use DeepWebSolutions\WC_Plugins\LockedPaymentMethods\Plugin;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Foundations\Logging\LoggingService;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Foundations\PluginInterface;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Helpers\Request;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Settings\SettingsService;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Validation\Handlers\ContainerValidationHandler;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\Utilities\Validation\ValidationService;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\WooCommerce\Logging\WC_LoggingHandler;
use DWS_LPMWC_Deps\DeepWebSolutions\Framework\WooCommerce\Settings\WC_SettingsHandler;
use DWS_LPMWC_Deps\DI\ContainerBuilder;
use function DWS_LPMWC_Deps\DI\factory;
use function DWS_LPMWC_Deps\DI\get;
use function DWS_LPMWC_Deps\DI\autowire;

defined( 'ABSPATH' ) || exit;

return array_merge(
	// Foundations
	array(
		PluginInterface::class => get( Plugin::class ),
		LoggingService::class  => factory(
			function( PluginInterface $plugin ) {
				$logging_handlers = array();
				$is_debug_active  = Request::has_debug( 'DWS_LPMWC_DEBUG' );

				if ( class_exists( 'WC_Log_Levels' ) ) { // in case the WC plugin is not active
					$min_log_level  = $is_debug_active ? WC_Log_Levels::DEBUG : WC_Log_Levels::ERROR;

					$logging_handlers = array(
						new WC_LoggingHandler( 'framework', null, $min_log_level ),
						new WC_LoggingHandler( 'plugin', null, $min_log_level ),
					);
				}

				return new LoggingService( $plugin, $logging_handlers, $is_debug_active );
			}
		),
	),
	// Settings
	array(
		'settings-validation-handler' => factory(
			function() {
				$config    = require_once __DIR__ . '/src/configs/settings.php';
				$container = ( new ContainerBuilder() )->addDefinitions( $config )->build();

				return new ContainerValidationHandler( 'settings', $container );
			}
		),

		SettingsService::class        => autowire( SettingsService::class )
			->method( 'register_handler', new WC_SettingsHandler() ),
		ValidationService::class      => autowire( ValidationService::class )
			->method( 'register_handler', get( 'settings-validation-handler' ) ),
	),
	// Plugin
	array(
		Plugin::class                   => autowire( Plugin::class )
			->constructorParameter( 'plugin_slug', 'locked-payment-methods-for-woocommerce' )
			->constructorParameter( 'plugin_file_path', dws_lpmwc_path() ),

		Settings::class                 => autowire( Settings::class )
			->constructorParameter( 'component_id', 'settings' )
			->constructorParameter( 'component_name', 'Settings' ),
		Settings\GeneralSettings::class => autowire( Settings\GeneralSettings::class )
			->constructorParameter( 'component_id', 'general-settings' )
			->constructorParameter( 'component_name', 'General Settings' ),
		Settings\PluginSettings::class  => autowire( Settings\PluginSettings::class )
			->constructorParameter( 'component_id', 'plugin-settings' )
			->constructorParameter( 'component_name', 'Plugin Settings' ),

		Permissions::class              => autowire( Permissions::class )
			->constructorParameter( 'component_id', 'permissions' )
			->constructorParameter( 'component_name', 'Permissions' ),

		LockManager::class              => autowire( LockManager::class )
			->constructorParameter( 'component_id', 'lock-manager' )
			->constructorParameter( 'component_name', 'Lock Manager' ),
	),
	// Plugin unlock strategies
	array(
		UnlockStrategies\UserMetaStrategy::class => autowire( UnlockStrategies\UserMetaStrategy::class )
			->constructorParameter( 'component_id', 'user-meta' )
			->constructorParameter( 'component_name', 'User Meta Unlock Strategy' ),
		UnlockStrategies\UserRoleStrategy::class => autowire( UnlockStrategies\UserRoleStrategy::class )
			->constructorParameter( 'component_id', 'user-role' )
			->constructorParameter( 'component_name', 'User Role Unlock Strategy' ),
	),
	// Plugin aliases
	array(
		'settings'           => get( Settings::class ),
		'general-settings'   => get( Settings\GeneralSettings::class ),
		'plugin-settings'    => get( Settings\PluginSettings::class ),

		'permissions'        => get( Permissions::class ),
		'lock-manager'       => get( LockManager::class ),

		'user-meta-strategy' => get( UnlockStrategies\UserMetaStrategy::class ),
		'user-role-strategy' => get( UnlockStrategies\UserRoleStrategy::class ),
	)
);
