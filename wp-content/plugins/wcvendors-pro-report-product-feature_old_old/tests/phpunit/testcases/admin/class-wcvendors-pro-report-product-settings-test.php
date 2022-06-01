<?php
/**
 * Tests for the Report Product settings class.
 *
 * @package WCVendors_Pro
 */

use Brain\Monkey\Functions;

/**
 * Report product settings test class.
 */
class WCVendors_Pro_Report_Product_Settings_Test extends WCVendorsTestCase {

	/**
	 * Set up test.
	 */
	public function setUp() : void {

		parent::setUp();

	}

	/**
	 * Test the report product reasons settings.
	 */
	public function test_report_product_reasons_setting() {
			Functions\expect( 'get_option' )->andReturn( array() );
			$reason_for_report_options = get_option( 'wcvendors_pro_report_product_reasons', array() );

			$this->assertEquals( $reason_for_report_options, array() );
	}

	/**
	 * Test the report product enable email setting
	 */
	public function test_enable_email_notification_setting() {
			Functions\expect( 'get_option' )->andReturn( 'yes' );
			$enable_email_notification_settings = get_option( 'wcvendors_pro_report_product_email_notification', 'yes' );

			$this->assertEquals( $enable_email_notification_settings, 'yes' );
	}
}
