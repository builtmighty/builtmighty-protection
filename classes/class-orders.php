<?php
/**
 * Orders.
 * 
 * A set methods to get and check orders for WooCommerce.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
class builtOrders {

    /**
     * Get customer orders.
     * 
     * @since   1.0.0
     */
    public function get_orders( $ip ) {

        // Check type.
        if( $this->storage() ) {

            // Get orders.
            $orders = $this->get_wc_orders( $ip );

        } else {

            // Get posts.
            $orders = $this->get_posts_orders( $ip );

        }

        // Return orders.
        return ( ! empty( $orders ) && is_array( $orders ) ) ? $orders : false;

    }

    /**
     * Get wp_posts orders.
     * 
     * @since   1.0.0
     */
    public function get_posts_orders( $ip ) {

        // Global.
        global $wpdb;

        // Set SQL.
        $SQL = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'shop_order' AND post_date > '" . date( 'Y-m-d H:i:s', strtotime( '-10 minutes' ) ) . "' AND post_author = '" . $ip . "'";

        // Get orders.
        $orders = $wpdb->get_results( $SQL );

        // Return orders.
        return $orders;

    }

    /**
     * Get wp_wc_orders.
     * 
     * @since   1.0.0
     */
    public function get_wc_orders( $ip ) {

        // Global.
        global $wpdb;

        // Set SQL..
        $SQL = "SELECT id FROM {$wpdb->prefix}wc_orders WHERE ip_address = '" . $ip . "' AND date_created_gmt > '" . date( 'Y-m-d H:i:s', strtotime( '-10 minutes' ) ) . "'";

        // Get orders.
        $orders = $wpdb->get_results( $SQL );

        // Return orders.
        return $orders;

    }

    /**
     * Get storage type.
     * 
     * Determines if site is using new WooCommerce tables or not.
     */
    public function storage() {

        // Global.
        global $wpdb;

        // Check if table exists.
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}wc_orders'" ) != $wpdb->prefix . 'wc_orders' ) return false;

        // Check if table has data.
        if( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_orders" ) == 0 ) return false;

        // Return true.
        return true;

    }

}