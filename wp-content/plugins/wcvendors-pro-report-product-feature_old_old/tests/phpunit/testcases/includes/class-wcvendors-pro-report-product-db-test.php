<?php
/**
 * Tests for the report product database class.
 *
 * @package WCVendors_Pro
 */

use Mockery;

/**
 * Class WCVendors_Pro_Report_Product_DB_Test
 */
class WCVendors_Pro_Report_Product_DB_Test extends WCVendorsTestCase {

	/**
	 * Setup
	 */
	public function setUp() : void {
		parent::setUp();
		$this->table_col    = array(
			'customer_id',
			'product_id',
			'report_date',
			'report_id',
			'report_notes',
			'report_reason',
			'report_response_notes',
			'report_status',
			'response_sent_to',
			'vendor_id',
		);
		$this->table_prefix = 'wp_';
	}

	/**
	 * Test if have create table function
	 */
	public function test_if_have_create_table_function() {
		require_once dirname( __FILE__, 5 ) . '/includes/wcv-update-functions.php';
		$this->assertTrue( function_exists( 'wcv_create_product_report_table' ) );
	}

	/**
	 * Test if table exists
	 */
	public function test_if_table_exists() {
		$wpdb = Mockery::mock( 'wpdb' );
		$wpdb->shouldReceive( 'get_var' )->andReturn( $this->table_prefix . 'wcv_product_report' );
		$table_name = $wpdb->get_var( 'SHOW TABLES LIKE "wp_wcv_product_report"' );
		$this->assertTrue( 'wp_wcv_product_report' === $table_name );
	}

	/**
	 * Test table columns exist
	 */
	public function test_table_columns_exist() {
		$wpdb = Mockery::mock( 'wpdb' );
		$wpdb->shouldReceive( 'get_col' )->andReturn( $this->table_col );
		$table_columns = $wpdb->get_col( "SHOW COLUMNS FROM `{$this->table_prefix}wcv_product_report`" );
		$this->assertEquals( $this->table_col, ( $table_columns ) );
	}
}
