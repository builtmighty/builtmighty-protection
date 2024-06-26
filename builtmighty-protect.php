<?php
/*
Plugin Name: 🛡️ Built Mighty Protect
Plugin URI: https://builtmighty.com
Description: Stop fraudulent orders in their tracks, with Built Mighty Protect.
Version: 0.0.1
Author: Built Mighty
Author URI: https://builtmighty.com
Copyright: Built Mighty
Text Domain: builtmighty-protect
Copyright © 2024 Built Mighty. All Rights Reserved.
*/

/**
 * Disallow direct access.
 * 
 * @since   1.0.0
 */
if( ! defined( 'WPINC' ) ) { die; }

/**
 * Constants.
 * 
 * @since   1.0.0
 */
define( 'BUILT_PROTECT_VERSION', '0.0.1' );
define( 'BUILT_PROTECT_NAME', 'builtmighty-protect' );
define( 'BUILT_PROTECT_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'BUILT_PROTECT_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'BUILT_PROTECT_DOMAIN', 'builtmighty-protect' );

/**
 * Stop if WooCommerce is not active.
 * 
 * @since   1.0.0
 */
if( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

/**
 * Load classes.
 * 
 * @since   1.0.0
 */
require_once BUILT_PROTECT_PATH . 'classes/core/class-db.php';
require_once BUILT_PROTECT_PATH . 'classes/users/class-mail.php';
require_once BUILT_PROTECT_PATH . 'classes/admin/class-admin.php';
require_once BUILT_PROTECT_PATH . 'classes/core/class-actions.php';
require_once BUILT_PROTECT_PATH . 'classes/core/class-assessment.php';
require_once BUILT_PROTECT_PATH . 'classes/core/class-detection.php';
require_once BUILT_PROTECT_PATH . 'classes/core/class-protection.php';
require_once BUILT_PROTECT_PATH . 'classes/admin/class-columns.php';
require_once BUILT_PROTECT_PATH . 'classes/users/class-orders.php';

/** 
 * On activation.
 * 
 * @since   1.0.0
 */
register_activation_hook( __FILE__, 'built_protect_activation' );
function built_protect_activation() {

    // Get database class.
    $db = new \BuiltMightyProtect\builtProtectionDB();

    // Create table.
    $db->create_table();

    // Flush rewrite rules.
    flush_rewrite_rules();

}

/**
 * On deactivation.
 * 
 * @since   1.0.0
 */
register_deactivation_hook( __FILE__, 'built_protect_deactivation' );
function built_protect_deactivation() {

    // Flush rewrite rules.
    flush_rewrite_rules();

}

/**
 * Initiate classes.
 * 
 * @since   1.0.0
 */
new \BuiltMightyProtect\builtAdmin();
new \BuiltMightyProtect\builtColumns();
new \BuiltMightyProtect\builtDetection();
new \BuiltMightyProtect\builtProtection();
new \BuiltMightyProtect\builtAssessment();
new \BuiltMightyProtect\builtOrders();

/** 
 * CLI.
 * 
 * @since   1.0.0
 */
if( defined( 'WP_CLI' ) && WP_CLI ) {

    // Register.
    add_action( 'plugins_loaded', 'built_protect_register_cli' );

}

/**
 * Register CLI.
 * 
 * @since   1.0.0
 */
function built_protect_register_cli() {

    // Require CLI classes.
    require_once BUILT_PROTECT_PATH . 'classes/cli/class-block.php';
    require_once BUILT_PROTECT_PATH . 'classes/cli/class-bypass.php';

    // Register CLI classes.
    WP_CLI::add_command( 'protect block', '\BuiltMightyProtect\builtCLIBlock' );
    WP_CLI::add_command( 'protect bypass', '\BuiltMightyProtect\builtCLIBypass' );

}


/**
 * Plugin Updates. 
 * 
 * @since   1.0.0
 */
require BUILT_PROTECT_PATH . 'updates/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$updates = PucFactory::buildUpdateChecker(
	'https://github.com/builtmighty/builtmighty-protection',
	__FILE__,
	'builtmighty-protection'
);
$updates->setBranch( 'main' );