<?php
/**
 * Actions.
 * 
 * Adds admin columns and actions for orders.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
class builtActions {

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Add columns.
        add_filter( 'woocommerce_shop_order_list_table_columns', [ $this, 'add_columns' ] );

        // Add data.
        add_action( 'render_built_order_column', [ $this, 'add_data' ] );

    }

    /**
     * Add columns.
     * 
     * @param   array   $columns    Columns.
     * 
     * @since   1.0.0
     */
    public function add_columns( $columns ) {

        error_log( __FUNCTION__ . ' is running.' );

        // Set new columns.
        $columns['built_order'] = __( 'Built Order', 'builtmighty' );

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
    public function add_data( $column ) {

        // Get order.
        $order = wc_get_order( get_the_ID() );

        echo 'HELLO';

        // Check column.
        if( $column == 'built_order' ) {

            // Get order.
            $order = wc_get_order( get_the_ID() );

            // Rating.
            echo ( $order->get_meta( 'built_order_rating' ) ) ? '<span class="built-yes">Yes</span>' : '<span class="built-no">No</span>';

        }

    }

}