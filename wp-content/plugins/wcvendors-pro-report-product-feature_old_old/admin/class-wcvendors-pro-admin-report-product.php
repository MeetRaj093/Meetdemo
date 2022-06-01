<?php

class WCVendors_Pro_Admin_Report_Product {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wcvendors_product_meta_start', array( $this, 'output' ) );
		add_action( 'admin_menu', array( $this, 'add_report_product_menu' ), 99 );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
	}

	/**
	 * Output the report table
	 */
	public function reports_table() {
		$report_product_table = new WCVendors_Pro_Report_Product_Table();

		wc_get_template(
			'html-report-product-table.php',
			array( 'report_product_table' => $report_product_table ),
			'wc-vendors/admin/report-product/',
			WCV_PRO_ABSPATH_ADMIN . 'views/report-product/'
		);
	}

	/**
	 * Add the report product menu item
	 */
	public function add_report_product_menu() {
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
	 * Report product table page screen options
	 */
	public function add_options() {
		$option = 'per_page';
		$args   = array(
			'label'   => __( 'Report product', 'wcvendors-pro' ),
			'default' => 10,
			'option'  => 'report_product_per_page',
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
		if ( 'report_product_per_page' == $option ) {
			return $value;
		}
	}
}
