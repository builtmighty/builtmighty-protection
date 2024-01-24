<?php
/**
 * Detection.
 * 
 * A set of rules and functions to detect fraudulent orders.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
class builtDetection {

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Order rate.
        add_action( 'woocommerce_checkout_order_processed', [ $this, 'order_rate' ] );

        // Failed payment rate.
        add_action( 'woocommerce_checkout_order_processed', [ $this, 'failed_rate' ] );

    }

    /**
     * Order rate.
     * 
     * Monitor and detect a high order rate from a single IP address.
     * 
     * @since   1.0.0
     */
    public function order_rate( $order_id, $data, $order ) {

        // Get orders.
        $o = new builtOrders();

        // Get customer IP.
        $ip     = $order->get_customer_ip_address();
        $time   = 10 * MINUTE_IN_SECONDS;
        $limit  = 5;

        // Get orders.
        $orders = $o->get_orders( $ip );

        // If the order rate is greater than the limit, cancel the order.
        if( count( $orders ) > $limit ) {

            // Cancel the order.
            $order->update_status( 'cancelled', __( 'Order cancelled due to suspected fraud.', 'builtmighty-protection' ) );

            // Add note.
            $order->add_order_note(
                sprintf(
                    __( 'Order cancelled due to suspected fraud. %s orders have been placed from this IP address in the last %s minutes.', 'builtmighty-protection' ),
                    count( $orders ),
                    $time / MINUTE_IN_SECONDS
                )
            );

            // Add IP to blacklist.
            $this->blacklist_ip( $ip );

        }

    }

    /**
     * Failed rate.
     * 
     * Monitor and detect a high failed rate for a WooCommerce session.
     * 
     * @since   1.0.0
     */
    public function failed_rate( $order_id ) {

        // Get order.
        $order = wc_get_order( $order_id );

        // Check if order failed.
        if( ! $order->has_status( 'failed' ) ) {

            // Reset failed attempts, because we had a successful order.
            WC()->session->set( 'failed_attempts', 0 );

        }

        // Get current attempts.
        $attempts = WC()->session->get( 'failed_attempts', 0 );

        // Increment current attempts and set.
        WC()->session->set( 'failed_attempts', $attempts++ );

        // If the failed rate is greater than the limit, cancel the order.
        if( $attempts > 3 ) {

            // Cancel the order.
            $order->update_status( 'cancelled', __( 'Order cancelled due to suspected fraud.', 'builtmighty-protection' ) );

            // Add note.
            $order->add_order_note(
                sprintf(
                    __( 'Order cancelled due to suspected fraud. %s failed attempts have been made in this session.', 'builtmighty-protection' ),
                    $attempts
                )
            );

            // Add IP to blacklist.
            $this->blacklist_ip( $ip );

        }

    }

    /**
     * Blacklist IP.
     * 
     * Add specified IP address to blacklist.
     * 
     * @since   1.0.0
     */
    public function blacklist_ip( $ip ) {

        // Get blacklist.
        $blacklist = ( ! empty( get_option( 'built_blacklist' ) ) ) ? get_option( 'built_blacklist' ) : [];

        // Check if IP is already blacklisted.
        if( in_array( $ip, $blacklist ) ) return;

        // Add IP to blacklist.
        $blacklist[] = $ip;

        // Update blacklist.
        update_option( 'built_blacklist', $blacklist );

    }


}