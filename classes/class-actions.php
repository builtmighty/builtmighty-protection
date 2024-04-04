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
     * Bypass.
     * 
     * @param   string  $ip         IP address to bypass.
     * 
     * @since   1.0.0
     */
    public function bypass_ip( $ip ) {

        // Database.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Set bypass query.
        $query = "SELECT id FROM $db->bypass WHERE ip = '$ip'";

        // Check if IP is already bypassed.
        if( $db->request( $query, 'row' ) ) return;

        // Set blocklist query.
        $query = "SELECT id FROM $db->blocklist WHERE ip = '$ip'";

        // Check if IP is already blocked.
        if( $db->request( $query, 'row' ) ) $this->blocklist_remove( $ip );

        // Set data.
        $data = [
            'ip'        => $ip,
            'date'      => date( 'Y-m-d H:i:s' )
        ];

        // Insert data.
        $db->insert( $db->bypass, $data );

    }

    /**
     * Bypass remove.
     * 
     * Remove IP address from bypass.
     * 
     * @param   string  $ip     IP address to remove.
     * 
     * @since   1.0.0
     */
    public function bypass_remove( $ip ) {

        // Database.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Remove from blocklist.
        $db->delete( $db->bypass, [ 'ip' => $ip ] );

    }

    /**
     * Blocklist IP.
     * 
     * Add specified IP address to blocklist.
     * 
     * @param   string  $ip         IP address to blocklist.
     * @param   int     $order_id   Order ID.
     * 
     * @since   1.0.0
     */
    public function blocklist_ip( $ip, $order_id = NULL ) {

        // Database.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Set bypass query.
        $query = "SELECT id FROM $db->bypass WHERE ip = '$ip'";

        // Check if IP is bypassed.
        if( $db->request( $query, 'row' ) ) return;

        // Set blocklist query.
        $query = "SELECT id FROM $db->blocklist WHERE ip = '$ip'";

        // Check if IP is already blocked.
        if( $db->request( $query, 'row' ) ) return;

        // Set data.
        $data = [
            'ip'        => $ip,
            'order_id'  => ( $order_id !== NULL ) ? $order_id : 1,
            'date'      => date( 'Y-m-d H:i:s' )
        ];

        // Insert data.
        $db->insert( $db->blocklist, $data );

    }

    /**
     * Blocklist remove.
     * 
     * Remove IP address from blocklist.
     * 
     * @param   string  $ip     IP address to remove.
     * 
     * @since   1.0.0
     */
    public function blocklist_remove( $ip ) {

        // Database.
        $db = new \BuiltMightyProtect\builtProtectionDB();

        // Remove from blocklist.
        $db->delete( $db->blocklist, [ 'ip' => $ip ] );

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