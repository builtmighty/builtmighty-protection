<?php
/**
 * Columns.
 * 
 * Adds admin columns and actions for orders.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
class builtColumns {

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Add columns.
        add_filter( 'woocommerce_shop_order_list_table_columns', [ $this, 'add_columns' ] );

        // Add data.
        add_action( 'manage_woocommerce_page_wc-orders_custom_column', [ $this, 'add_data' ], 10, 2 );

        // Admin styles.
        add_action( 'admin_footer', [ $this, 'admin_styles' ] );

    }

    /**
     * Add columns.
     * 
     * @param   array   $columns    Columns.
     * 
     * @since   1.0.0
     */
    public function add_columns( $columns ) {

        // Set new columns.
        $columns['built_order'] = __( '<span style="width:100%;display:block;text-align:center;">🛡️</span>', 'builtmighty' );

        // Return columns.
        return $columns;

    }

    /**
     * Add data.
     * 
     * @param   string  $column     Column.
     * 
     * @since   1.0.0
     */
    public function add_data( $column, $order ) {

        // Check column.
        if( $column == 'built_order' ) {

            // Get assessment.
            $assess = new \BuiltMightyProtect\builtAssessment();

            // Output.
            echo $assess->get_assessment( $order->get_id() );

        }

    }

    /**
     * Admin styles.
     * 
     * @since   1.0.0
     */
    public function admin_styles() { ?>
    
        <style>.built-protect-rating-bar{height:10px;background:rgb(0 0 0 / 15%);width:100px;border-radius:6px;padding:2px}.built-protect-rating-bar-inner{height:8px;padding:1px;border-radius:6px;max-width:99px}.built-protect-rating-bar-number{font-weight:700;font-size:12px}</style><?php

    }

}