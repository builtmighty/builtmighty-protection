<?php
/**
 * Protection.
 * 
 * Block blacklisted IPs from accessing the site.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
use WC_Geolocation;
class builtProtection {

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Load on init.
        add_action( 'init', [ $this, 'protect' ] );

    }

    /**
     * Protect.
     * 
     * Block bad IPs.
     * 
     * @since   1.0.0
     */
    public function protect() {

        // Check for blacklisted IP.
        if( $this->block( $this->get_ip() ) ) {

            // Die.
            wp_die( 'Access denied. You have been blocked from accessing this site.' );

        }

    }

    /**
     * Get IP.
     * 
     * Get the current user IP address.
     * 
     * @since   1.0.0
     */
    public function get_ip() {

        // Get and return IP.
        return ( NULL !== WC_Geolocation::get_ip_address() ) ? WC_Geolocation::get_ip_address() : $_SERVER['REMOTE_ADDR'];

    }
    
    /**
     * Check if user is blocked.
     * 
     * Check the list of blacklisted IPs.
     * 
     * @param   string  $ip IP address to check.
     * 
     * @since   1.0.0
     */
    public function block( $ip ) {

        // Get database class.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Get blacklist.
        $blacklist = $db->request( "SELECT id FROM {$db->table} WHERE ip = '{$ip}'", 'row' );

        // Return.
        return ( empty( $blacklist ) ) ? false : true;

    }

}