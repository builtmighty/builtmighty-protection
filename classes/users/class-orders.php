<?php
/**
 * Orders.
 * 
 * A set methods to get and check orders for WooCommerce.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
class builtOrders {

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Actions.
        add_action( 'add_meta_boxes', [ $this, 'add_metabox' ], 10, 1 );
        add_action( 'woocommerce_process_shop_order_meta', [ $this, 'order_save' ], 10, 1 );

    }

    /**
     * Add meta box.
     * 
     * @since   1.0.0
     */
    public function add_metabox() {

        // Check storage.
        if( $this->hpos() ) {

            // Add metabox.
            add_meta_box( 
                'built_protect', 
                '🛡️ Protect', 
                [ $this, 'metabox_content' ],
                'woocommerce_page_wc-orders', 
                'side', 
                'high' 
            );

        } else {

            // Add legacy metabox.
            add_meta_box( 
                'built_protect', 
                '🛡️ Protect', 
                [ $this, 'metabox_content' ], 
                'shop_order', 
                'side', 
                'high' 
            );

        }

    }

    /**
     * Metabox content.
     * 
     * @since   1.0.0
     */
    public function metabox_content() {

        // Check storage.
        if( $this->hpos() ) {

            // Global.
            global $post, $thepostid, $theorder;

            // Get order.
            $order = $theorder;

        } else {

            // Global.
            global $post;

            // Get order.
            $order = wc_get_order( $post->ID );

        }

        // Classes.
        $assess = new \BuiltMightyProtect\builtAssessment();
        $status = new \BuiltMightyProtect\builtProtection();

        // Wrap. ?>
        <div class="built-protect-order built-protect-order-rating"><?php

            // Check for rating.
            if( $assess->get_rating( $order->get_id() ) == 'unrated' ) {

                // Get assessment. ?>
                <form method="post">
                    <input type="hidden" name="built_assess" value="<?php echo $order->get_id(); ?>">
                    <button type="submit" class="button button-primary">Assess Order</button>
                </form><?php

            } else {

                // Output assessment.
                echo $assess->get_assessment( $order->get_id() );

            } ?>

        </div><?php

        // Check if order is administrator.
        if( $order->get_customer_id() !== 0 ) {

            // Get user.
            $user = get_user_by( 'ID', $order->get_customer_id() );

            // If user is admin, stop.
            if( in_array( 'administrator', $user->roles ) ) return;

        } ?>

        <div class="built-protect-order built-protect-order-actions" data-ip="<?php echo $order->get_customer_ip_address(); ?>"><?php

            // Check if blocked.
            if( $status->block( $order->get_customer_ip_address() ) ) {

                // Blocked. ?>
                <div class="built-protect-order-status" style="font-weight:bold;color:red">
                    <span class="dashicons dashicons-no-alt"></span> Blocked
                </div><?php

                // Unblock. ?>
                <form method="post">
                    <button type="submit" name="built_unblock" value="<?php echo $order->get_customer_ip_address(); ?>" class="button button-primary">Unblock IP</button>
                </form><?php

            } else {

                // Not blocked. ?>
                <div class="built-protect-order-status" style="font-weight:bold;color:green">
                    <span class="dashicons dashicons-yes-alt"></span> Not Blocked
                </div><?php

                // Block. ?>
                <form method="post">
                    <button type="submit" name="built_block" value="<?php echo $order->get_customer_ip_address(); ?>" class="button button-primary">Block IP</button>
                </form><?php

            } ?>

        </div>
        <style>.built-protect-order{margin:15px 0 0}.built-protect-order-actions{display:flex;align-items:center}.built-protect-order-actions>button,.built-protect-order-actions>div{flex:1}</style><?php

    }

    /**
     * On order save.
     * 
     * @param   int     $order_id   Order ID.
     * 
     * @since   1.0.0
     */
    public function order_save( $order_id ) {

        // Get order.
        $order = wc_get_order( $order_id );

        // Get user.
        $user = get_user_by( 'ID', get_current_user_id() );

        // Check for assessment.
        if( ! empty( $_POST['built_assess'] ) ) {

            // Get assessment.
            $assess = new \BuiltMightyProtect\builtAssessment();

            // Add an order note of who assessed.
            $order->add_order_note( 'Order assessment triggered by <a href="' . admin_url( '/user-edit.php?user_id=' . $user->ID ) . '" target="_blank">' . $user->user_login . '</a>.' );

            // Assess order.
            $assess->assess_order( (int)$_POST['built_assess'] );

        } elseif( ! empty( $_POST['built_block'] ) ) {

            // Actions.
            $action = new \BuiltMightyProtect\builtActions();

            // Add an order note of who blocked.
            $order->add_order_note( 'Customer of order <strong style="color:red">blocked</strong> by <a href="' . admin_url( '/user-edit.php?user_id=' . $user->ID ) . '" target="_blank">' . $user->user_login . '</a>.' );

            // Add IP to blocklist.
            $action->blocklist_ip( $_POST['built_block'], $_POST['post_ID'] );

        } elseif( ! empty( $_POST['built_unblock'] ) ) {

            // Actions.
            $action = new \BuiltMightyProtect\builtActions();

            // Add an order note of who unblocked.
            $order->add_order_note( 'Customer of order <strong style="color:green">unblocked</strong> by <a href="' . admin_url( '/user-edit.php?user_id=' . $user->ID ) . '" target="_blank">' . $user->user_login . '</a>.' );

            // Remove IP from blocklist.
            $action->blocklist_remove( $_POST['built_unblock'] );

        }

    }

    /**
     * Get customer orders.
     * 
     * @param   string  $ip     IP address.
     * 
     * @since   1.0.0
     */
    public function get_orders( $ip ) {

        // Check type.
        if( $this->hpos() ) {

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
     * @param   string  $ip IP address.
     * 
     * @since   1.0.0
     */
    public function get_posts_orders( $ip ) {

        // Global.
        global $wpdb;

        // Set minutes.
        $minutes = ( ! empty( get_option( 'built_order_time' ) ) ) ? get_option( 'built_order_time' ) : 10;

        // Set time.
        $time = date( 'Y-m-d H:i:s', strtotime( '-' . $minutes . ' minutes' ) );

        // Set SQL.
        $SQL = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'shop_order' AND post_date > '" . $time . "' AND post_author = '" . $ip . "'";

        // Get orders.
        $orders = $wpdb->get_results( $SQL );

        // Return orders.
        return $orders;

    }

    /**
     * Get wp_wc_orders.
     * 
     * @param   string  $ip IP address.
     * 
     * @since   1.0.0
     */
    public function get_wc_orders( $ip ) {

        // Global.
        global $wpdb;

        // Set minutes.
        $minutes = ( ! empty( get_option( 'built_order_time' ) ) ) ? get_option( 'built_order_time' ) : 10;

        // Set time.
        $time = date( 'Y-m-d H:i:s', strtotime( '-' . $minutes . ' minutes' ) );

        // Set SQL..
        $SQL = "SELECT id FROM {$wpdb->prefix}wc_orders WHERE ip_address = '" . $ip . "' AND date_created_gmt > '" . $time . "'";

        // Get orders.
        $orders = $wpdb->get_results( $SQL );

        // Return orders.
        return $orders;

    }

    /**
     * Check for high-performance order storage.
     * 
     * Determines if site is using new WooCommerce HPOS.
     * 
     * @since   1.0.0
     */
    public function hpos() {

        // Global.
        global $wpdb;

        // Check if in HPOS mode.
        if( get_option( 'woocommerce_custom_orders_table_enabled' ) == 'yes' ) return true;

        // Check if table exists.
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}wc_orders'" ) != $wpdb->prefix . 'wc_orders' ) return false;

        // Check if table has data.
        if( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_orders" ) == 0 ) return false;

        // Return true.
        return true;

    }

}