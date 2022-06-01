<?php
use Brain\Monkey\Functions;
class WCVendors_Pro_Product_Controller_Test extends WCVendorsTestCase {
	public function test_maybe_disable_actions_do_nothing() {
		$actions = array(
			'edit' => array(),
			'view' => array(),
		);
		$product = Mockery::mock( 'WC_Product' );
		$product->shouldReceive( 'get_meta' )->andReturn( 'no' );
		$controller = new WCVendors_Pro_Product_Controller( new stdClass(), '1.0', false );

		$this->assertEquals( $actions, $controller->maybe_disable_actions( $actions, $product ) );
	}

	public function test_maybe_disable_actions_unset_edit() {
		$actions = array(
			'edit' => array(),
			'view' => array(),
		);
		$product = Mockery::mock( 'WC_Product' );
		$product->shouldReceive( 'get_meta' )->andReturn( 'yes' );
		$controller = new WCVendors_Pro_Product_Controller( new stdClass(), '1.0', false );

		$this->assertArrayNotHasKey( 'edit', $controller->maybe_disable_actions( $actions, $product ) );
	}

	public function test_maybe_disable_page() {
		$controller = new WCVendors_Pro_Product_Controller( new stdClass(), '1.0', false );
		$this->assertEquals( true, $controller->maybe_disable_page( true, 'order' ) );
		$this->assertEquals( false, $controller->maybe_disable_page( false, 'order' ) );

		$product = Mockery::mock( 'WC_Product' );
		$product->shouldReceive( 'get_meta' )->andReturn( 'yes' );
		Functions\expect( 'get_query_var' )->once()->with( Mockery::type( 'string' ) )->andReturn( '1' );
		Functions\expect( 'wc_get_product' )->once()->with( Mockery::type( 'string' ) )->andReturn( $product );

		$this->assertTrue( $controller->maybe_disable_page( false, 'product' ) );
	}
}
