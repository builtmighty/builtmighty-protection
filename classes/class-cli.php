<?php
/**
 * CLI.
 * 
 * Create CLI commands for protection.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
class builtCLI {

    /**
     * Bypass command.
     * 
     * wp protect bypass --ip=value
     */
    public function bypass( $args, $assoc_args ) {

        // Get IP.
        $ip = $assoc_args['ip'];

        // Add IP to bypass.
        $action = new builtActions();
        $action->bypass_ip( $ip );

        // Success.
        \WP_CLI::success( 'IP added to bypass list.' );

    }

    /**
     * Remove bypass.
     * 
     * Remove the IP from the bypass list.
     * 
     * wp protect bypass_remove --ip=value
     * 
     * @since   1.0.0
     */
    public function bypass_remove( $args, $assoc_args ) {

        // Get IP.
        $ip = $assoc_args['ip'];

        // Remove IP from bypass.
        $action = new builtActions();
        $action->bypass_remove( $ip );

        // Success.
        \WP_CLI::success( 'IP removed from bypass list.' );

    }

    /**
     * Blocklist command.
     * 
     * wp protect block --ip=value
     */
    public function blocklist( $args, $assoc_args ) {

        // Get IP.
        $ip = $assoc_args['ip'];

        // Add IP to blocklist.
        $action = new builtActions();
        $action->blocklist_ip( $ip );

        // Success.
        \WP_CLI::success( 'IP added to blocklist.' );

    }

    /** 
     * Block remove.
     * 
     * Remove IP from blocklist.
     * 
     * wp protect block_remove --ip=value
     * 
     * @since   1.0.0
     */
    public function blocklist_remove( $args, $assoc_args ) {

        // Get IP.
        $ip = $assoc_args['ip'];

        // Remove IP from blocklist.
        $action = new builtActions();
        $action->blocklist_remove( $ip );

        // Success.
        \WP_CLI::success( 'IP removed from blocklist.' );

    }

}