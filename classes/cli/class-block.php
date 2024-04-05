<?php
/**
 * CLI block.
 * 
 * Create CLI commands for protection.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
class builtCLIBlock {

    /**
     * Blocklist command.
     * 
     * wp protect block add --ip=value
     */
    public function add( $args, $assoc_args ) {

        // Check for IP.
        if( ! isset( $assoc_args['ip'] ) ) {

            // Error.
            \WP_CLI::error( 'Please provide an IP address. Use --ip=value' );

        }

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
     * wp protect block remove --ip=value
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

        // Remove IP from blocklist.
        $action = new builtActions();
        $action->blocklist_remove( $ip );

        // Success.
        \WP_CLI::success( 'IP removed from blocklist.' );

    }

    /**
     * Get block list.
     * 
     * Get the block list.
     * 
     * wp protect block list
     * 
     * @since   1.0.0
     */
    public function list( $args, $assoc_args ) {

        // Get database class.
        $db = new \BuiltMightyProtect\builtProtectionDB();
        
        // Get recent blocklist.
        $query = "SELECT `ip` FROM $db->blocklist ORDER BY id DESC LIMIT 10"; 
        
        // Get blocklist.
        $blocklist = $db->request( $query, 'results' );

        // Check blocklist.
        if( ! empty( $blocklist ) ) {

            // Output blocklist.
            \WP_CLI::line( 'Blocklist:' );
            foreach( $blocklist as $ip ) {
                \WP_CLI::line( '• ' . $ip['ip'] );
            }

        } else {

            // No blocklist.
            \WP_CLI::line( 'Blocklist is empty.' );

        }

    }

}