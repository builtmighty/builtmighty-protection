<?php
/**
 * Admin.
 * 
 * Configuration settings for the plugin.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
use WC_Geolocation;
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
        $tabs['built_settings'] = __( '🛡️ Built Mighty', BUILT_PROTECT_DOMAIN );

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

        // Add.
        $this->add( $_POST );

        // Settings.
        woocommerce_admin_fields( $this->get_settings() );

        // Bypass.
        $this->get_bypass();

        // Blocklist.
        $this->get_blocklist();

    }

    /**
     * Bypass.
     * 
     * @since   1.0.0
     */
    public function get_bypass() {

        // Start output buffering.
        ob_start();

        // Get database class.
        $db = new \BuiltMightyProtect\builtProtectionDB();
        
        // Get recent blocklist.
        $query = "SELECT * FROM $db->bypass ORDER BY id DESC LIMIT 10"; 
        
        // Get bypass.
        $bypass = $db->request( $query, 'results' );
        
        // Get IP.
        $ip = ( NULL !== WC_Geolocation::get_ip_address() ) ? WC_Geolocation::get_ip_address() : $_SERVER['REMOTE_ADDR']; ?>

        <h2>Bypass</h2>
        <p>Add an IP address to the bypass, so that it isn't blocked from accessing the site. Your IP is: <code><?php echo $ip; ?></code><?php

        // Add to bypass. ?>
        <div class="built-bypass-add">
            <form method="post">
                <input type="text" name="built-bypass-ip" placeholder="IP Address">
                <input type="submit" class="button-primary woocommerce-save-button" name="built-bypass-add" value="+">
            </form>
        </div><?php

        // Check if empty.
        if( empty( $bypass ) ) {

            // Output. ?>
            <div class="built-bypass built-bypass-empty">
                <p>Bypass Empty</p>
            </div><?php
            
        } else {

            // Output. ?>
            <table class="built-bypass-table">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody><?php

                    // Loop through bypass.
                    foreach( $bypass as $item ) { ?>

                        <tr>
                            <td><?php echo $item['ip']; ?></td>
                            <td><?php echo $item['date']; ?></td>
                            <td><input type="submit" name="built-bypass-remove-<?php echo $item['id']; ?>" value="×"></td>
                        </tr><?php

                    } ?>

                </tbody>
            </table><?php

        }
        
        // Styles. ?>
        <style>table.built-bypass-table,.built-bypass-empty{background:#2c3338;width:100%;color:#fff;padding:15px;border-radius:6px;text-align:left}table.built-bypass-table thead th{background:#1d2327;border-radius:6px}table.built-bypass-table tbody td,table.built-bypass-table thead th{padding:5px 10px}table.built-bypass-table tbody td{border-bottom:1px solid rgb(255 255 255 / 10%)}table.built-bypass-table tbody tr:last-child td{border-bottom:none!important}table.built-bypass-table tbody td input[type=submit]{background:red;border-radius:100%;border:1px solid red;color:#fff;display:flex;width:24px;height:24px;align-content:center;justify-content:center;font-weight:700;line-height:1;transition:.3s;-webkit-transition:.3s;-moz-transition:.3s;cursor:pointer}table.built-bypass-table tbody td input[type=submit]:hover{background:0 0}.built-bypass-add{margin: 0 0 15px 0;}.built-bypass-empty{width: calc(100% - 30px);}</style><?php

        // Output.
        echo ob_get_clean();

    }

    /**
     * Blocklist.
     * 
     * @since   1.0.0
     */
    public function get_blocklist() {

        // Start output buffering.
        ob_start();

        // Get database class.
        $db = new \BuiltMightyProtect\builtProtectionDB();
        
        // Get recent blocklist.
        $query = "SELECT * FROM $db->blocklist ORDER BY id DESC LIMIT 10"; 
        
        // Get blocklist.
        $blocklist = $db->request( $query, 'results' ); ?>

        <h2>Blocklist</h2><?php

        // Check if empty.
        if( empty( $blocklist ) ) {

            // Output. ?>
            <div class="built-blocklist built-blocklist-empty">
                <p>Blocklist Empty</p>
            </div><?php
            
        } else {

            // Output. ?>
            <table class="built-blocklist-table">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Order ID</th>
                        <th>Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody><?php

                    // Loop through blocklist.
                    foreach( $blocklist as $item ) { ?>

                        <tr>
                            <td><?php echo $item['ip']; ?></td>
                            <td><a href="<?php echo admin_url( '/admin.php?page=wc-orders&action=edit&id=' . $item['order_id'] ); ?>" style="text-decoration:none;color:red" target="_blank">#<?php echo $item['order_id']; ?></a></td>
                            <td><?php echo $item['date']; ?></td>
                            <td><input type="submit" name="built-remove-<?php echo $item['id']; ?>" value="×"></td>
                        </tr><?php

                    } ?>

                </tbody>
            </table><?php

        }
        
        // Styles. ?>
        <style>table.built-blocklist-table,.built-blocklist-empty{background:#2c3338;width:100%;color:#fff;padding:15px;border-radius:6px;text-align:left}table.built-blocklist-table thead th{background:#1d2327;border-radius:6px}table.built-blocklist-table tbody td,table.built-blocklist-table thead th{padding:5px 10px}table.built-blocklist-table tbody td{border-bottom:1px solid rgb(255 255 255 / 10%)}table.built-blocklist-table tbody tr:last-child td{border-bottom:none!important}table.built-blocklist-table tbody td input[type=submit]{background:red;border-radius:100%;border:1px solid red;color:#fff;display:flex;width:24px;height:24px;align-content:center;justify-content:center;font-weight:700;line-height:1;transition:.3s;-webkit-transition:.3s;-moz-transition:.3s;cursor:pointer}table.built-blocklist-table tbody td input[type=submit]:hover{background:0 0}.built-blocklist-empty{width: calc(100% - 30px);}</style><?php

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
            'section_assess_title' => [
                'name'      => __( 'Order Assessment', BUILT_PROTECT_DOMAIN ),
                'type'      => 'title',
                'desc'      => 'Assess orders based on multiple criteria, for possible fraud.',
                'id'        => 'assess_section_title'
            ],
            [
                'name'      => __( 'Enable', BUILT_PROTECT_DOMAIN ),
                'type'      => 'checkbox',
                'desc'      => 'Enable Order Assessment',
                'id'        => 'built_assess_rate'
            ],
            [
                'name'      => __( 'Proxy Check', BUILT_PROTECT_DOMAIN ),
                'type'      => 'password',
                'desc'      => 'Sign-up for a <a href="https://proxycheck.io/" target="_blank">proxycheck.io</a> API key, which has 1,000 free checks per day.',
                'id'        => 'built_proxycheck_key'
            ],
            'section_assess_end' => [
                'type'      => 'sectionend',
                'id'        => 'wc_settings_assess_end'
            ],
        ];

        // Return.
        return apply_filters( 'wc_settings_built_tab', $settings );

    }

    /**
     * Add IP to bypass.
     * 
     * @param   array   $data   POST data.
     * 
     * @since   1.0.0
     */
    public function add( $data ) {

        // Loop through data.
        foreach( $_POST as $key => $value ) {

            // Check if remove.
            if( strpos( $key, 'built-bypass-ip' ) !== false ) {

                // Get IP.
                $ip = $value;

                // Actions.
                $action = new \BuiltMightyProtect\builtActions();

                // Add.
                $action->bypass_ip( $ip );

            }

        }

    }

    /**
     * Remove IP from blocklist or bypass.
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

                // Load database class.
                $db = new \BuiltMightyProtect\builtProtectionDB();

                // Remove.
                $db->delete( $db->blocklist, [ 'id' => (int)$id ] );

            } elseif( strpos( $key, 'built-bypass-remove' ) !== false ) {

                // Get the ID.
                $id = str_replace( 'built-bypass-remove-', '', $key );

                // Load the database class.
                $db = new \BuiltMightyProtect\builtProtectionDB();

                // Remove.
                $db->delete( $db->bypass, [ 'id' => (int)$id ] );

            }

        }

    } 

}