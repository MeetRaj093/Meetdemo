<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */


add_action( 'woocommerce_before_calculate_totals', 'misha_recalc_price' );

function misha_recalc_price( $cart_object ) {
    foreach ( $cart_object->get_cart() as $hash => $value ) {
        $value['data']->set_price( 10 );
    }
}





add_action( 'woocommerce_before_calculate_totals', 'misha_recalculate_price' );
 
function misha_recalculate_price( $cart_object ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    // you can always print_r() your object and look what's inside
    //print_r( $cart_object ); exit;
 
    // it is our quantity of products in specific product category
    $quantity = 0;
 
    // $hash = cart item unique hash
    // $value = cart item data
    foreach ( $cart_object->get_cart() as $hash => $value ) {
 
 
        // check if the product is in a specific category and check if its ID isn't 12345
        if( in_array( 25, $value['data']->get_category_ids() ) && $value['product_id'] != 1037 ) {
        
            // if yes, count its quantity
            $quantity += $value['quantity'];
            
        }


    }
 
    // change prices
    if( $quantity > 3 ) {
        foreach ( $cart_object->get_cart() as $hash => $value ) {
 
 
            // I want to make discounts only for products in category with ID 25
            // and I never want to make discount for the product with ID 12345
            if( in_array( 25, $value['data']->get_category_ids() ) && $value['product_id'] != 1037 ) {
            
                $newprice = $value['data']->get_regular_price() / 2;
                
                // in case the product is already on sale and it is much cheeper with its own discount
                if( $value['data']->get_sale_price() > $newprice )
                    $value['data']->set_price( $newprice );
                    
            }
            
        }
    }
 
}

function enable_cot(){
    $order_controller = wc_get_container()
        ->get('Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController');
    if( isset( $order_controller ) ) {
        $order_controller->show_feature();
    }
}
add_action( 'init', 'enable_cot', 99 );
