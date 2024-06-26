<?php
/**
 * Detection.
 * 
 * A set of rules and functions to detect fraudulent orders.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
class builtDetection {

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Check if order rate is enabled.
        if( get_option( 'built_order_rate' ) === 'yes' ) {

            // Order rate.
            add_action( 'woocommerce_new_order', [ $this, 'order_rate' ], 10, 1 );

        }

        // Check if failure rate is enabled.
        if( get_option( 'built_failed_rate' ) === 'yes' ) {

            // Failed payment rate.
            add_action( 'woocommerce_new_order', [ $this, 'failed_rate' ], 10, 1 );

        }

    }

    /**
     * Order rate.
     * 
     * Monitor and detect a high order rate from a single IP address.
     * 
     * @param   int     $order_id   Order ID.
     * 
     * @since   1.0.0
     */
    public function order_rate( $order_id ) {

        // Disable on admin side.
        if( is_admin() ) return;

        // Check if order was placed by admin user.
        if( is_user_logged_in() && current_user_can( 'manage_options' ) ) return;

        // Get orders.
        $o = new \BuiltMightyProtect\builtOrders();

        // Get customer IP.
        $ip     = $order->get_customer_ip_address();
        $limit  = ( ! empty( get_option( 'built_order_limit' ) ) ) ? get_option( 'built_order_limit' ) : 5;
        $time   = ( ! empty( get_option( 'built_order_time' ) ) ) ? get_option( 'built_order_time' ) * MINUTE_IN_SECONDS : 10 * MINUTE_IN_SECONDS;

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

            // Actions.
            $action = new \BuiltMightyProtect\builtActions();

            // Add IP to blocklist.
            $action->blocklist_ip( $ip, $order_id );

        }

    }

    /**
     * Failed rate.
     * 
     * Monitor and detect a high failed rate for a WooCommerce session.
     * 
     * @param   int     $order_id   Order ID.
     * 
     * @since   1.0.0
     */
    public function failed_rate( $order_id ) {

        // Disable on admin side.
        if( is_admin() ) return;

        // CHeck if order was placed by admin user.
        if( is_user_logged_in() && current_user_can( 'manage_options' ) ) return;

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

        // Set limit.
        $limit = ( ! empty( get_option( 'built_failed_limit' ) ) ) ? get_option( 'built_failed_limit' ) : 3;

        // If the failed rate is greater than the limit, cancel the order.
        if( $attempts > $limit ) {

            // Cancel the order.
            $order->update_status( 'cancelled', __( 'Order cancelled due to suspected fraud.', 'builtmighty-protection' ) );

            // Add note.
            $order->add_order_note(
                sprintf(
                    __( 'Order cancelled due to suspected fraud. %s failed attempts have been made in this session.', 'builtmighty-protection' ),
                    $attempts
                )
            );

            // Actions.
            $action = new \BuiltMightyProtect\builtActions();

            // Add IP to blocklist.
            $action->blocklist_ip( $ip, $order_id );

        }

    }

}