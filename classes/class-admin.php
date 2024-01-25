<?php
/**
 * Admin.
 * 
 * Configuration settings for the plugin.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtection;
class builtAdmin {

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Add tab.
        add_filter( 'woocommerce_settings_tabs_array', [ $this, 'built_tab' ], 50 );

        // Load settings.
        add_action( 'woocommerce_settings_built_settings', [ $this, 'built_settings' ] );

        // Save settings.
        add_action( 'woocommerce_update_options_built_settings', [ $this, 'save_settings' ] );

    }

    /**
     * Settings tab.
     * 
     * @since   1.0.0
     */
    public function built_tab( $tabs ) {

        // Add.
        $tabs['built_settings'] = __( 'Built Mighty', BUILT_PROTECT_DOMAIN );

        // Return.
        return $tabs;

    }

    /**
     * Settings.
     * 
     * @since   1.0.0
     */
    public function built_settings() {

        // Remove.
        $this->remove( $_POST );

        // Settings.
        woocommerce_admin_fields( $this->get_settings() );

        // Blacklist.
        $this->get_blacklist();

    }

    /**
     * Blacklist.
     * 
     * @since   1.0.0
     */
    public function get_blacklist() {

        // Start output buffering.
        ob_start();

        // Get database class.
        $db = new \BuiltMightyProtection\builtProtectionDB();
        
        // Get recent blacklist.
        $query = "SELECT * FROM $db->table ORDER BY id DESC LIMIT 10"; 
        
        // Get blacklist.
        $blacklist = $db->request( $query, 'results' ); ?>

        <h2>Blacklist</h2><?php

        // Check if empty.
        if( empty( $blacklist ) ) {

            // Output. ?>
            <div class="built-blacklist built-blacklist-empty">
                <p>Blacklist Empty</p>
            </div><?php
            
        } else {

            // Output. ?>
            <form class="built-blacklist" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Order ID</th>
                            <th>Time</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody><?php

                        // Loop through blacklist.
                        foreach( $blacklist as $item ) { ?>

                            <tr>
                                <td><?php echo $item['ip']; ?></td>
                                <td><?php echo $item['order_id']; ?></td>
                                <td><?php echo $item['date']; ?></td>
                                <td><input type="submit" name="built-remove-<?php echo $item['id']; ?>" value="×"></td>
                            </tr><?php

                        } ?>

                    </tbody>
                </table>
            </form><?php

        }

        // Output.
        echo ob_get_clean();

    }

    /**
     * Save settings.
     * 
     * @since   1.0.0
     */
    public function save_settings() {

        // Save.
        woocommerce_update_options( $this->get_settings() );

    }

    /**
     * Get settings.
     * 
     * @since   1.0.0
     */
    public function get_settings() {

        // Set.
        $settings = [
            'section_order_title' => [
                'name'      => __( 'Order Rate', BUILT_PROTECT_DOMAIN ),
                'type'      => 'title',
                'desc'      => 'Detect fraudulent orders based on the rate of orders from a single IP address.',
                'id'        => 'orders_section_title'
            ],
            [
                'name'      => __( 'Enable', BUILT_PROTECT_DOMAIN ),
                'type'      => 'checkbox',
                'desc'      => 'Enable Order Rate Limiting',
                'id'        => 'built_order_rate'
            ],
            [
                'name'      => __( 'Order Rate Time', BUILT_PROTECT_DOMAIN ),
                'type'      => 'number',
                'desc'      => 'The time block in which to check for an order rate. Default is 10 minutes.',
                'id'        => 'built_order_time'
            ],
            [
                'name'      => __( 'Order Rate Limit', BUILT_PROTECT_DOMAIN ),
                'type'      => 'number',
                'desc'      => 'The maximum rate of orders allowed to be placed within the order rate time. Default is 5 orders.',
                'id'        => 'built_order_limit'
            ],
            'section_order_end' => [
                'type'      => 'sectionend',
                'id'        => 'wc_settings_order_end'
            ],
            'section_failed_title' => [
                'name'      => __( 'Failed Rate', BUILT_PROTECT_DOMAIN ),
                'type'      => 'title',
                'desc'      => 'Detect fraudulent orders based on the failure rate of orders in a single WooCommerce session.',
                'id'        => 'failed_section_title'
            ],
            [
                'name'      => __( 'Enable', BUILT_PROTECT_DOMAIN ),
                'type'      => 'checkbox',
                'desc'      => 'Enable Failure Rate Limiting',
                'id'        => 'built_failed_rate'
            ],
            [
                'name'      => __( 'Failure Rate Limit', BUILT_PROTECT_DOMAIN ),
                'type'      => 'number',
                'desc'      => 'The maximum rate of failed orders allowed to be placed in a single WooCommerce session. Default is 5 orders.',
                'id'        => 'built_failed_limit'
            ],
            'section_failed_end' => [
                'type'      => 'sectionend',
                'id'        => 'wc_settings_failed_end'
            ],
        ];

        // Return.
        return apply_filters( 'wc_settings_built_tab', $settings );

    }

    /**
     * Remove IP from blacklist.
     * 
     * @param   array   $data   POST data.
     * 
     * @since   1.0.0
     */
    public function remove( $data ) {

        // Loop through data.
        foreach( $_POST as $key => $value ) {

            // Check if remove.
            if( strpos( $key, 'built-remove-' ) !== false ) {

                // Get ID.
                $id = str_replace( 'built-remove-', '', $key );

                error_log( 'ID to remove: ' . $id );

                // Load database class.
                $db = new \BuiltMightyProtection\builtProtectionDB();

                // Remove.
                $db->delete( [ 'id' => (int)$id ] );

            }

        }

    } 

}