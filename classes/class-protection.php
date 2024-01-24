<?php
/**
 * Protection.
 * 
 * Block blacklisted IPs from accessing the site.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
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
        if( in_array( $this->get_ip(), (array)$this->get_blacklist() ) ) {

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
     * Get blacklist.
     * 
     * Get the list of blacklisted IPs.
     * 
     * @since   1.0.0
     */
    public function get_blacklist() {

        // If the blacklist is empty, set it to an empty array.
        if( empty( get_option( 'built_blacklist' ) ) ) return false;

        // Remove empty values.
        $blacklist = array_filter( get_option( 'built_blacklist' ) );

        // Return blacklist.
        return get_option( 'built_blacklist' );

    }

}