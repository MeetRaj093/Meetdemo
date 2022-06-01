<?php
use Brain\Monkey\Functions;

/**
 * Class Pro_Admin_Shipping_Test
 */
class Pro_Admin_Shipping_Test extends WCVendorsTestCase {
	/**
	 *  SetUp .
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
	}
	/**
	 * Test if rates include customer country or region
	 *
	 * @return void
	 */
	public function test_rates_include_country_or_region() {
		Mockery::mock( 'WC_Shipping_Method' );
		$rates = array(
			array(
				'country'   => 'US',
				'region'    => '',
				'state'     => '',
				'post_code' => '',
				'fee'       => 45,
			),
			array(
				'country'   => '',
				'region'    => 'AS',
				'state'     => '',
				'post_code' => '',
				'fee'       => 50,
			),
		);

		$pro_shipping = Mockery::mock( WCVendors_Pro_Shipping_Method::class )->makePartial();

		$customer_country         = 'US';
		$customer_region          = 'AS';
		$is_rates_include_country = $pro_shipping::check_rates_include_country( $rates, $customer_country );
		$is_rates_include_region  = $pro_shipping::check_rates_include_customer_region( $rates, $customer_region );

		$this->assertTrue( $is_rates_include_country );
		$this->assertTrue( $is_rates_include_region );
	}
	/**
	 * Test get all country in rates
	 *
	 * @return void
	 */
	public function test_get_rates_countries() {
		Mockery::mock( 'WC_Shipping_Method' );
		$rates = array(
			array(
				'country'   => 'US',
				'region'    => '',
				'state'     => '',
				'post_code' => '',
				'fee'       => 45,
			),
			array(
				'country'   => '',
				'region'    => 'AS',
				'state'     => '',
				'post_code' => '',
				'fee'       => 50,
			),
			array(
				'country'   => 'AU',
				'region'    => '',
				'state'     => '',
				'post_code' => '',
				'fee'       => 35,
			),
		);

		$pro_shipping    = Mockery::mock( WCVendors_Pro_Shipping_Method::class )->makePartial();
		$array_countries = array_map( array( $pro_shipping, 'get_array_of_table_country' ), $rates );

		$this->assertIsArray( $array_countries );
		$this->assertContains( 'au', $array_countries );

	}
}
