<?php
/**
 * Protection.
 * 
 * Block IPs from accessing the site.
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

        // Load on wp.
        add_action( 'wp', [ $this, 'protect' ] );

        // Prevent order placement.
        add_action( 'woocommerce_checkout_process', [ $this, 'protect_orders' ] );

    }

    /**
     * Protect.
     * 
     * Block bad IPs.
     * 
     * @since   1.0.0
     */
    public function protect() {

        // Check for blocklisted IP.
        if( $this->block( $this->get_ip() ) ) {

            // Die.
            wp_die( 'Access denied. You have been blocked from accessing this site. If you think this has been done in error, please contact us.' );

        }

    }

    /**
     * Protect orders.
     * 
     * Block bad IPs.
     * 
     * @since   1.0.0
     */
    public function protect_orders() {

        // Check for blocklisted IP.
        if( $this->block( $this->get_ip() ) ) {

            // Block the checkout process and notify the user.
            wc_add_notice( __( 'Access denied. You have been blocked from placing orders on this site. If you think this has been done in error, please contact us.', BUILT_PROTECT_NAME ), 'error' );

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
     * Check the list of blocklisted IPs.
     * 
     * @param   string  $ip IP address to check.
     * 
     * @since   1.0.0
     */
    public function block( $ip ) {

        // Get database class.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Get blocklist.
        $blocklist = $db->request( "SELECT id FROM {$db->blocklist} WHERE ip = '{$ip}'", 'row' );

        // Return.
        return ( empty( $blocklist ) ) ? false : true;

    }

}