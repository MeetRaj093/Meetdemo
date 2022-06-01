<?php

use Brain\Monkey\Functions;

class WCVendors_Pro_Setting_Forms_Test extends WCVendorsTestCase {

	public function setUp() : void {

		parent::setUp();
		Functions\expect( 'get_option' )->andReturn( 'yes' );
	}
	public function test_wcvendors_hide_product_shipping_weight() {

		$is_product_shipping_weight_disabled = get_option( 'wcvendors_hide_product_shipping_weight', 'no' );

		$this->assertEquals( $is_product_shipping_weight_disabled, 'yes' );
	}
	public function test_wcvendors_hide_product_shipping_dimensions() {

		$is_product_shipping_dimessions_disabled = get_option( 'wcvendors_hide_product_shipping_dimensions', 'no' );

		$this->assertEquals( $is_product_shipping_dimessions_disabled, 'yes' );
	}

	public function test_wcvendors_hide_product_shipping_shipping_class() {

		$is_product_shipping_shipping_class_disabled = get_option( 'wcvendors_hide_product_shipping_shipping_class', 'no' );

		$this->assertEquals( $is_product_shipping_shipping_class_disabled, 'yes' );
	}
}
