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
