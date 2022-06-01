<?php

class WCVendors_Pro_Admin_Product_Reports {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wcvendors_product_meta_start', array( $this, 'output' ) );
		add_action( 'admin_menu', array( $this, 'add_product_reports_menu' ), 99 );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
	}

	/**
	 * Output the report table
	 */
	public function reports_table() {
		$product_reports_table = new WCVendors_Pro_Product_Reports_Table();

		wc_get_template(
			'html-product-reports-table.php',
			array( 'product_reports_table' => $product_reports_table ),
			'wc-vendors/admin/product-reports/',
			WCV_PRO_ABSPATH_ADMIN . 'views/product-reports/'
		);
	}

	/**
	 * Add the report product menu item
	 */
	public function add_product_reports_menu() {
		$hook = add_submenu_page(
			'wc-vendors',
			__( 'Product Reports', 'wcvendors-pro' ),
			__( 'Product Reports', 'wcvendors-pro' ),
			'manage_woocommerce',
			'wcv-reports-product',
			array( $this, 'reports_table' ),
		);
		add_action( "load-$hook", array( $this, 'add_options' ) );
	}

	/**
	 * Product Reports table page screen options
	 */
	public function add_options() {
		$option = 'per_page';
		$args   = array(
			'label'   => __( 'Product Reports', 'wcvendors-pro' ),
			'default' => 10,
			'option'  => 'product_reports_per_page',
		);
		add_screen_option( $option, $args );
	}

	/**
	 * Set the screen options for the report product table
	 *
	 * @param  string     $status  Status of the screen option.
	 * @param  string     $option  option name.
	 * @param  string|int $value option value.
	 */
	public function set_screen_option( $status, $option, $value ) {
		if ( 'product_reports_per_page' == $option ) {
			return $value;
		}
	}
}
