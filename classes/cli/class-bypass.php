<?php
/**
 * CLI bypass.
 * 
 * Create CLI commands for protection.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
class builtCLIBypass {

    /**
     * Add to bypass list.
     * 
     * Add an IP to the bypass list.
     * 
     * wp protect bypass add --ip=value
     */
    public function add( $args, $assoc_args ) {

        // Check for IP.
        if( ! isset( $assoc_args['ip'] ) ) {

            // Error.
            \WP_CLI::error( 'Please provide an IP address. Use --ip=value' );

        }

        // Get IP.
        $ip = $assoc_args['ip'];

        // Add IP to bypass.
        $action = new builtActions();
        $action->bypass_ip( $ip );

        // Success.
        \WP_CLI::success( 'IP added to bypass list.' );

    }

    /**
     * Remove from bypass list.
     * 
     * Remove the IP from the bypass list.
     * 
     * wp protect bypass remove --ip=value
     * 
     * @since   1.0.0
     */
    public function remove( $args, $assoc_args ) {

        // Check for IP.
        if( ! isset( $assoc_args['ip'] ) ) {

            // Error.
            \WP_CLI::error( 'Please provide an IP address. Use --ip=value' );

        }

        // Get IP.
        $ip = $assoc_args['ip'];

        // Remove IP from bypass.
        $action = new builtActions();
        $action->bypass_remove( $ip );

        // Success.
        \WP_CLI::success( 'IP removed from bypass list.' );

    }

    /**
     * Get bypass list.
     * 
     * Get the bypass list.
     * 
     * wp protect bypass list
     * 
     * @since   1.0.0
     */
    public function list( $args, $assoc_args ) {

        // Get database class.
        $db = new \BuiltMightyProtect\builtProtectionDB();
        
        // Get recent bypass.
        $query = "SELECT `ip` FROM $db->bypass ORDER BY id DESC LIMIT 10"; 
        
        // Get bypass.
        $bypass = $db->request( $query, 'results' );

        // Check bypass.
        if( ! empty( $bypass ) ) {

            // Output bypass.
            \WP_CLI::line( 'Bypass:' );
            foreach( $bypass as $ip ) {
                \WP_CLI::line( '• ' . $ip['ip'] );
            }

        } else {

            // No bypass.
            \WP_CLI::line( 'Bypass is empty.' );

        }

    }

}