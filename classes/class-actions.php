<?php
/**
 * Actions.
 * 
 * Different actions across the plugin.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
class builtActions {

    /**
     * Whitelist.
     * 
     * @param   string  $ip         IP address to whitelist.
     * 
     * @since   1.0.0
     */
    public function whitelist_ip( $ip ) {

        // Database.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Set whitelist query.
        $query = "SELECT id FROM $db->whitelist WHERE ip = '$ip'";

        // Check if IP is already whitelisted.
        if( $db->request( $query, 'row' ) ) return;

        // Set blacklist query.
        $query = "SELECT id FROM $db->protect WHERE ip = '$ip'";

        // Check if IP is already blacklisted.
        if( $db->request( $query, 'row' ) ) $this->blacklist_remove( $ip );

        // Set data.
        $data = [
            'ip'        => $ip,
            'date'      => date( 'Y-m-d H:i:s' )
        ];

        // Insert data.
        $db->insert( $db->whitelist, $data );

    }

    /**
     * Whitelist remove.
     * 
     * Remove IP address from whitelist.
     * 
     * @param   string  $ip     IP address to remove.
     * 
     * @since   1.0.0
     */
    public function whitelist_remove( $ip ) {

        // Database.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Remove from blacklist.
        $db->delete( $db->whitelist, [ 'ip' => $ip ] );

    }

    /**
     * Blacklist IP.
     * 
     * Add specified IP address to blacklist.
     * 
     * @param   string  $ip         IP address to blacklist.
     * @param   int     $order_id   Order ID.
     * 
     * @since   1.0.0
     */
    public function blacklist_ip( $ip, $order_id ) {

        // Database.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Set whitelist query.
        $query = "SELECT id FROM $db->whitelist WHERE ip = '$ip'";

        // Check if IP is whitelisted.
        if( $db->request( $query, 'row' ) ) return;

        // Set blacklist query.
        $query = "SELECT id FROM $db->protect WHERE ip = '$ip'";

        // Check if IP is already blacklisted.
        if( $db->request( $query, 'row' ) ) return;

        // Set data.
        $data = [
            'ip'        => $ip,
            'order_id'  => $order_id,
            'date'      => date( 'Y-m-d H:i:s' )
        ];

        // Insert data.
        $db->insert( $db->protect, $data );

    }

    /**
     * Blacklist remove.
     * 
     * Remove IP address from blacklist.
     * 
     * @param   string  $ip     IP address to remove.
     * 
     * @since   1.0.0
     */
    public function blacklist_remove( $ip ) {

        // Database.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Remove from blacklist.
        $db->delete( $db->protect, [ 'ip' => $ip ] );

    }

    /**
     * Assess the order.
     * 
     * Monitor and assess the order for potential fraud.
     * 
     * @param   int     $order_id   Order ID.
     * 
     * @since   1.0.0
     */
    public function assess_order( $order_id ) {

        // Assess.
        $assess = new \BuiltMightyProtect\builtAssessment();

        // Assess order.
        $assess->assess_order( $order_id );

    }

}