<?php
/*
Plugin Name: 🛡️ Built Mighty Protection
Plugin URI: https://builtmighty.com
Description: Stop fraudulent orders in their tracks, with Built Mighty Protection.
Version: 0.0.1
Author: Built Mighty
Author URI: https://builtmighty.com
Copyright: Built Mighty
Text Domain: builtmighty-protection
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
define( 'BUILT_PROTECT_NAME', 'builtmighty-protection' );
define( 'BUILT_PROTECT_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'BUILT_PROTECT_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'BUILT_PROTECT_DOMAIN', 'builtmighty-protection' );

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
require_once BUILT_PROTECT_PATH . 'classes/class-db.php';
require_once BUILT_PROTECT_PATH . 'classes/class-admin.php';
require_once BUILT_PROTECT_PATH . 'classes/class-orders.php';
require_once BUILT_PROTECT_PATH . 'classes/class-detection.php';
require_once BUILT_PROTECT_PATH . 'classes/class-protection.php';

/** 
 * On activation.
 * 
 * @since   1.0.0
 */
register_activation_hook( __FILE__, 'built_protect_activation' );
function built_protect_activation() {

    // Get database class.
    $db = new \BuiltMightyProtection\builtProtectionDB();

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
new \BuiltMightyProtection\builtAdmin();
new \BuiltMightyProtection\builtDetection();
new \BuiltMightyProtection\builtProtection();

/**
 * Plugin Updates. 
 * 
 * @since   1.0.0
 */
// require BUILT_PROTECT_PATH . 'updates/plugin-update-checker.php';
// use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
// $updates = PucFactory::buildUpdateChecker(
// 	'https://github.com/builtmighty/builtmighty-kit',
// 	__FILE__,
// 	'builtmighty-kit'
// );
// $updates->setBranch( 'main' );