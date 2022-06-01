<?php
/**
 *  WC Vendors Template
 *
 *  Functions for the WC Vendors template system
 *
 * @package WC_Vendors_Pro\Functions
 * @since 1.7.9
 * @version 1.7.9
 */

if ( ! function_exists( 'wcv_get_default_store_banner_src' ) ) {
	/**
	 * Get defualt vendor banner src
	 *
	 * @return array
	 * @version 1.7.9
	 * @since   1.7.9
	 */
	function wcv_get_default_store_banner_src() {

		$defaul_banner_src = plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/images/wcvendors_default_banner.jpg';

		return apply_filters( 'wcv_get_default_store_banner_src', $defaul_banner_src );
	}
}

/**
 * Output the import/export buttons on the product table actions partial
 */
add_action( 'wcv_product_action_after_buttons', 'wcv_output_import_export_buttons' );
if ( ! function_exists( 'wcv_output_import_export_buttons' ) ) {
	function wcv_output_import_export_buttons() {

		$button_html       = '';
		$buttons           = wcv_get_import_export_buttons();
		$lock_new_product  = wc_string_to_bool( get_user_option( '_wcv_lock_new_products_vendor' ) );
		$lock_edit_product = wc_string_to_bool( get_user_option( '_wcv_lock_edit_products_vendor' ) );

		// disable import button if not enabled
		if ( ! wc_string_to_bool( get_option( 'wcvendors_capability_products_import', false ) ) ) {
			unset( $buttons['import'] );
		}

		// Disable import button if vendor is locked from adding or editing products.
		if ( $lock_new_product && $lock_edit_product ) {
			unset( $buttons['import'] );
		}

		// Disable export button if not enabled
		if ( ! wc_string_to_bool( get_option( 'wcvendors_capability_products_export', false ) ) ) {
			unset( $buttons['export'] );
		}

		foreach ( $buttons as $key => $button ) {
			$button_html .= '<a href="' . $button['url'] . '" class="' . $button['css_class'] . '">' . $button['label'] . '</a> ';
		}

		echo $button_html;
	}
}

if ( ! function_exists( 'wcv_output_vendor_ga_code' ) ) {

	/**
	 * Output the vendor tracking code
	 *
	 * @param int $vendor_id - the vendor user ID.
	 * @return string $ga_code - the google analytics code
	 */
	function wcv_output_vendor_ga_code( $vendor_id ) {

		// Not a vendor? return nothing.
		if ( ! WCV_Vendors::is_vendor( $vendor_id ) ) {
			return '';
		}

		$vendor_tracking_id = get_user_meta( $vendor_id, '_wcv_settings_ga_tracking_id', true );

		// No tracking code added, return nothing.
		if ( empty( $vendor_tracking_id ) ) {
			return '';
		}

		$ga_code = sprintf(
		' <!-- Global site tag (gtag.js) - Google Analytics added by WC Vendors Pro -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=' . $vendor_tracking_id . '"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag(\'js\', new Date());
		
		  gtag(\'config\', \' ' . $vendor_tracking_id . ' \');
		</script> '
		);

		return $ga_code;
	}
}
//add_filter( 'woocommerce_loop_product_link', 'wcvendors_single_product_permalink', 99, 2 );

// function wcvendors_single_product_permalink( $link, $product ) {
// global $product;
// $vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
// $vendor_id = get_the_author_meta('ID');
// $store_url = WCVendors_Pro_Vendor_Controller::get_vendor_store_url( $vendor_id );
// $shop_name = get_user_meta( $vendor_id, 'pv_shop_name', true );
// $store_link = '<a href="'.$store_url.'">'.$shop_name.'</a>';
// //$this_product_id = $product->get_id();



// $product_slug = get_post_field('post_name', $product_id);
// echo '<a href="' . esc_url( $link) .($vendor_shop). '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
// //$product_permalink = $product->get_permalink();
// //$slug = $product->post_name;

// //$url = home_url();
// //echo $url;

// // echo "<pre>";
// // print_r($url);
// // echo "</pre>";



// if ( $this_product_id === 1123 ) $link = '/custom-landing-page';





// return $link;
// }


// function wcvendor_prefix_filter_vendor_permalink( $post_link, $post, $leavename, $sample ) {
//     if('product' == get_post_type($post->ID)){
//     	global $product;
//          $vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
//         if(!empty($vendor_shop) && false !== strpos($post_link, '%vendor_shop%')){
//             $post_link = str_replace('%vendor_shop%', $brands[0]->slug, $post_link);


//         }else{
//             $post_link = str_replace('%vendor_shop%/', '', $post_link);
//               print_r($post_link);
//         }
//     }
//     return $post_link;
// }
// add_filter( 'post_type_link', 'wcvendor_prefix_filter_vendor_permalink', 10, 4 );


	//  function wcvendor_change_archive_link( $link , $product ) {

	// 	global $product;
	// 	$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
	// 	$vendor_id   = WCV_Vendors::get_vendor_id( $vendor_shop );
		

	// 	//print_r($vendor_id);


	// 	return ! $vendor_id ? $link : WCV_Vendors::get_vendor_shop_page( $vendor_id );
	// }

	// add_filter( 'woocommerce_loop_product_link', 'wcvendor_change_archive_link', 99, 2 );


function wc_vendor_single_vendor_post_link( $post_link, $id = 0 ){
		$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
		$vendor_id   = WCV_Vendors::get_vendor_id( $vendor_shop );
   // $post = get_post($id);  
    if ( is_object( $vendor_shop ) ){
        $terms = wp_get_object_terms( $vendor_id->ID, 'vendor_shop' );
        if( $terms ){
            return str_replace( '%vendor_shop%' , $terms[0]->slug , $post_link );
        }
    }
    return $post_link;  
}
add_filter( 'post_type_link', 'wc_vendor_single_vendor_post_link', 1, 3 );








// function wpse221475_redirect_request($wp) {

//     if ( ! empty($wp->request) && $wp->request === 'projects/special-project' ) {

//         wp_redirect(home_url('special-project'), 301);
//         exit;

//     }

// }

// add_action('wp', 'wpse221475_redirect_request');