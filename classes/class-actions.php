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
        add_action( 'manage_woocommerce_page_wc-orders_custom_column', [ $this, 'add_data' ], 10, 2 );

        // Add metabox to orders.
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );

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
        $columns['built_order'] = __( '<span style="width:100px;display:block;text-align:center;">🛡️</span>', 'builtmighty' );

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

            // Set rating.
            $rating = ( ! empty( $order->get_meta( 'built_order_rating' ) ) ) ? $order->get_meta( 'built_order_rating' ) : 0;

            // Output. ?>
            <div class="built-protect-rating">
                <div class="built-protect-rating-bar">
                    <div class="built-protect-rating-bar-inner" data-rating="<?php echo $rating; ?>" style="width:<?php echo $rating; ?>%;background:<?php echo $this->get_color( $rating ); ?>"></div>
                </div>
                <div class="built-protect-rating-bar-number" style="color:<?php echo $this->get_color( $rating ); ?>"><?php echo $rating; ?>%</div>
            </div><?php

        }

    }

    /**
     * Add meta box to orders.
     * 
     * @since   1.0.0
     */
    public function add_meta_box() {

        // Add metabox.
        add_meta_box( 'built_order_rating', __( 'Order Rating', 'builtmighty' ), [ $this, 'meta_box' ], 'shop_order', 'side', 'core' );

    }

    /**
     * Admin styles.
     * 
     * @since   1.0.0
     */
    public function admin_styles() { ?>
    
        <style>.built-protect-rating-bar{height:10px;background:rgb(0 0 0 / 15%);width:100px;border-radius:6px;padding:2px}.built-protect-rating-bar-inner{height:8px;padding:1px;border-radius:6px;max-width:99px}.built-protect-rating-bar-number{font-weight:700;font-size:12px}</style><?php

    }

    /**
     * Get color.
     * 
     * @since   1.0.0
     */
    public function get_color( $rating ) {

        // Set color scale.
        if( $rating >= 90 ) {

            // Set to green.
            $color = '#0d8c2d';
            
        } elseif( $rating >= 80 && $rating < 90 ) {

            // Set to light green.
            $color = '#578c0d';

        } elseif( $rating >= 70 && $rating < 80 ) {

            // Set to yellow-green.
            $color = '#798c0d';

        } elseif( $rating >= 60 && $rating < 70 ) {

            // Set to yellow.
            $color = '#8c7f0d';

        } elseif( $rating >= 50 && $rating < 60 ) {

            // Set to yellow-orange.
            $color = '#8c5b0d';

        } elseif( $rating >= 40 && $rating < 50 ) {

            // Set to orange.
            $color = '#8c3b0d';

        } elseif( $rating < 40 ) {

            // Set to red.
            $color = '#8c0d0d';

        }

        // Return color.
        return $color;

    }

}