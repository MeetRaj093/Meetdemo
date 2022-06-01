<?php
use Brain\Monkey\Functions;

class WCVendors_Pro_Delivery_Test extends WCVendorsTestCase {

	public $actions = array(
		'pay'    => array(
			'url'  => 'https://example.com/pay',
			'name' => 'Pay',
		),
		'view'   => array(
			'url'  => 'https://example.com/view',
			'name' => 'View',
		),
		'cancel' => array(
			'url'  => 'https://example.com/cancel',
			'name' => 'Cancel',
		),
	);

	public function test_enqueue_scripts_not_run_on_pages_are_not_myaccount() {
		Functions\expect( 'get_queried_object_id' )
			->once()
			->andReturn( 11 );

		Functions\expect( 'wc_get_page_id' )
			->once()
			->andReturn( 10 );

		( new WCVendors_Pro_Delivery() )->enqueue_scripts();
	}

	public function test_enqueue_scripts() {
		Functions\expect( 'get_queried_object_id' )
			->once()
			->andReturn( 10 );

		Functions\expect( 'wc_get_page_id' )
			->once()
			->andReturn( 10 );

		Functions\expect( 'wp_enqueue_script' )
			->once()
			->andReturnTrue();

		Functions\expect( 'wp_localize_script' )
			->once()
			->andReturnTrue();

		Functions\expect( 'wp_create_nonce' )
			->atLeast()
			->once()
			->with( 'wcv-mark-order-received' )
			->andReturn( 'new_nonce' );

		( new WCVendors_Pro_Delivery() )->enqueue_scripts();
	}

	public function test_add_orders_list_action_not_add_if_order_is_received() {
		$instance    = new WCVendors_Pro_Delivery();
		$order       = Mockery::mock( 'WC_Order' );
		$item        = Mockery::mock( 'WC_Order_Item_Product' );
		$wcv_vendors = Mockery::mock( 'alias:WCV_Vendors' )->makePartial();

		$order->shouldReceive( 'get_meta' )
			  ->atLeast()
			  ->once()
			  ->with( '_wcv_order_received' )
			  ->andReturn( array( 2 ) );
		$order->shouldReceive( 'get_items' )
			  ->atLeast()
			  ->once()
			  ->andReturn( array( $item ) );

		$item->shouldReceive( 'get_product_id' )
			 ->andReturn( 10 );

		$wcv_vendors->shouldReceive( 'get_vendor_from_product' )
					->andReturn( 2 );

		$actions = $instance->add_orders_list_action( $this->actions, $order );

		$this->assertEquals( 3, count( $actions ) );
		$this->assertArrayHasKey( 'pay', $actions );
		$this->assertArrayHasKey( 'view', $actions );
		$this->assertArrayHasKey( 'cancel', $actions );
	}

	public function test_add_orders_list_action_not_add_if_order_is_refunded() {
		$instance = new WCVendors_Pro_Delivery();
		$order    = Mockery::mock( 'WC_Order' );

		$order->shouldReceive( 'get_meta' )
			  ->atLeast()
			  ->once()
			  ->with( '_wcv_order_received' )
			  ->andReturn( array() );
		$order->shouldReceive( 'get_total_refunded' )
			  ->atLeast()
			  ->once()
			  ->andReturn( 1 );

		$actions = $instance->add_orders_list_action( $this->actions, $order );
		$this->assertEquals( 3, count( $actions ) );
	}

	public function test_add_orders_list_action_not_add_if_orders_has_invalid_status() {
		$instance = new WCVendors_Pro_Delivery();
		$order    = Mockery::mock( 'WC_Order' );

		$order->shouldReceive( 'get_meta' )
			  ->atLeast()
			  ->once()
			  ->with( '_wcv_order_received' )
			  ->andReturn( array() );
		$order->shouldReceive( 'get_total_refunded' )
			  ->atLeast()
			  ->once()
			  ->andReturn( 0 );
		$order->shouldReceive( 'get_status' )
			  ->andReturn( 'pending' );

		$actions = $instance->add_orders_list_action( $this->actions, $order );
		$this->assertEquals( 3, count( $actions ) );
	}

	public function test_add_orders_list_action_added() {
		$_SERVER     = array(
			'HTTPS'       => 'on',
			'HTTP_HOST'   => 'wcvendors.local',
			'REQUEST_URI' => '/my-account/orders/',
		);
		$order       = Mockery::mock( 'WC_Order' );
		$item        = Mockery::mock( 'WC_Order_Item_Product' );
		$wcv_vendors = Mockery::mock( 'alias:WCV_Vendors' )->makePartial();

		$item->shouldReceive( 'get_product_id' )
			 ->andReturn( 10 );

		$wcv_vendors->shouldReceive( 'get_vendor_from_product' )
					->andReturn( 2 );

		$order
			->shouldReceive( 'get_meta' )
			->atLeast()
			->once()
			->with( '_wcv_order_received' )
			->andReturn( array() );
		$order->shouldReceive( 'get_total_refunded' )
			  ->atLeast()
			  ->once()
			  ->andReturn( 0 );
		$order->shouldReceive( 'get_status' )
			  ->atLeast()
			  ->once()
			  ->andReturn( 'processing', 'completed' );
		$order->shouldReceive( 'get_id' )
			  ->atLeast()
			  ->once()
			  ->andReturn( 1 );
		$order->shouldReceive( 'get_items' )
			  ->withNoArgs()
			  ->atLeast()
			  ->once()
			  ->andReturn( array( $item ) );
		$order->shouldReceive( 'get_items' )
			  ->with( 'shipping' )
			  ->atLeast()
			  ->once()
			  ->andReturn( array( 'shipping_item' ) );

		Functions\expect( 'wc_get_account_endpoint_url' )
			->atLeast()
			->once()
			->with( 'orders' )
			->andReturn( 'https://example.com/mark' );
		Functions\expect( 'add_query_arg' )
			->atLeast()
			->once()
			->with( Mockery::type( 'array' ), Mockery::type( 'string' ) )
			->andReturn( 'http://example.com' );
		Functions\expect( 'wp_create_nonce' )
			->atLeast()
			->once()
			->with( 'wcv-mark-order-received' )
			->andReturn( 'new_nonce' );

		$instance = new WCVendors_Pro_Delivery();
		$actions  = $instance->add_orders_list_action( $this->actions, $order );
		$actions2 = $instance->add_orders_list_action( $this->actions, $order );

		$this->assertEquals( 4, count( $actions ) );
		$this->assertEquals( 4, count( $actions2 ) );

		foreach ( $actions as $action ) {
			$this->assertArrayHasKey( 'url', $action );
			$this->assertArrayHasKey( 'name', $action );
		}
	}

	public function test_mark_received_return_false_if_nonce_is_missing() {
		$instance = new WCVendors_Pro_Delivery();
		$this->assertFalse( $instance->mark_received() );
	}

	public function test_mark_received_return_false_if_nonce_is_invalid() {
		$_GET = array( 'wcv_nonce' => 'a823hr9' );

		Functions\stubs(
			array(
				'sanitize_key',
			)
		);
		Functions\expect( 'wp_verify_nonce' )
			->once()
			->andReturnFalse();

		$instance = new WCVendors_Pro_Delivery();
		$this->assertFalse( $instance->mark_received() );
	}

	public function test_mark_received_return_false_if_no_order_is_provided() {
		$_GET = array(
			'wcv_nonce' => 'a823hr9',
			'order'     => '',
		);

		Functions\stubs(
			array(
				'sanitize_key',
			)
		);
		Functions\expect( 'wp_verify_nonce' )
			->once()
			->andReturnTrue();

		$instance = new WCVendors_Pro_Delivery();
		$this->assertFalse( $instance->mark_received() );
	}

	public function test_mark_received_success() {
		$_GET = array(
			'wcv_nonce' => 'a823hr9',
			'order'     => 1,
			'vendor'    => 2,
		);

		Functions\stubs(
			array(
				'sanitize_key',
			)
		);

		$order       = Mockery::mock( 'WC_Order' );
		$wcv_vendors = Mockery::mock( 'alias:WCV_Vendors' )->makePartial();

		$wcv_vendors->shouldReceive( 'get_vendor_shop_name' )
					->andReturn( 'Vendor One' );
		$wcv_vendors->shouldReceive( 'get_vendor_from_product' )
					->andReturn( 2 );

		$item = Mockery::mock( 'WC_Order_Item_Product' );
		$item->shouldReceive( 'get_product_id' )
			 ->andReturn( 10 );

		$order->shouldReceive( 'get_items' )
			  ->withNoArgs()
			  ->once()
			  ->andReturn( array( $item ) );
		$order
			->shouldReceive( 'update_meta_data' )
			->once()
			->andReturnTrue();
		$order->shouldReceive( 'get_id' )
			  ->atLeast()
			  ->once()
			  ->andReturn( 1 );
		$order->shouldReceive( 'get_meta' )
			  ->atLeast()
			  ->once()
			  ->with( '_wcv_order_received' )
			  ->andReturn( array() );
		$order->shouldReceive( 'get_total_refunded' )
			  ->atLeast()
			  ->once()
			  ->andReturn( 0 );
		$order->shouldReceive( 'get_status' )
			  ->atLeast()
			  ->once()
			  ->andReturn( 'processing', 'completed' );
		$order
			->shouldReceive( 'add_order_note' )
			->once()
			->andReturnTrue();
		$order
			->shouldReceive( 'set_status' )
			->andReturnTrue();
		$order
			->shouldReceive( 'save' )
			->andReturnTrue();
		$order->shouldReceive( 'get_items' )
			  ->with( 'shipping' )
			  ->atLeast()
			  ->once()
			  ->andReturn( array( 'shipping_item' ) );

		Functions\expect( 'wp_verify_nonce' )
			->once()
			->andReturnTrue();
		Functions\expect( 'wc_get_order' )
			->once()
			->andReturn( $order );
		Functions\expect( 'wc_add_notice' )
			->once()
			->andReturn( true );

		$instance = new WCVendors_Pro_Delivery();
		$this->assertTrue( $instance->mark_received() );
	}

	public function test_print_received_text() {
		Functions\expect( 'wc_get_order_status_name' )
			->once()
			->andReturn( 'Processing' );

		$item = Mockery::mock( 'WC_Order_Item_Product' );
		$item->shouldReceive( 'get_product_id' )
			 ->andReturn( 10 );

		$order = Mockery::mock( 'WC_Order' );
		$order->shouldReceive( 'get_status' )
			  ->once()
			  ->andReturn( 'processing' );
		$order->shouldReceive( 'get_meta' )
			  ->once()
			  ->with( '_wcv_order_received' )
			  ->andReturn( array( 2 ) );
		$order->shouldReceive( 'get_items' )
			  ->withNoArgs()
			  ->once()
			  ->andReturn( array( $item ) );

		$wcv_vendors = Mockery::mock( 'alias:WCV_Vendors' )->makePartial();
		$wcv_vendors->shouldReceive( 'get_vendor_from_product' )
					->andReturn( 2 );

		$instance = new WCVendors_Pro_Delivery();
		$this->expectOutputRegex( '/.*Received.*/', $instance->print_received_text( $order ) );
	}

	public function test_print_received_text_for_vendor() {
		$new_row         = new stdClass();
		$new_row->status = 'Yes';
		$_order          = new stdClass();

		$item = Mockery::mock( 'WC_Order_Item_Product' );
		$item->shouldReceive( 'get_product_id' )
			 ->andReturn( 10 );

		$order = Mockery::mock( 'WC_Order' );
		$order->shouldReceive( 'get_meta' )
			  ->once()
			  ->with( '_wcv_order_received' )
			  ->andReturn( array( 2 ) );
		$order->shouldReceive( 'get_items' )
			  ->withNoArgs()
			  ->once()
			  ->andReturn( array( $item ) );

		$wcv_vendors = Mockery::mock( 'alias:WCV_Vendors' )->makePartial();
		$wcv_vendors->shouldReceive( 'get_vendor_from_product' )
					->andReturn( 2 );

		$instance = new WCVendors_Pro_Delivery();
		$output   = $instance->print_received_text_for_vendor( $new_row, $_order, $order );
		$this->assertStringContainsString( 'Received', $output->status );
	}
}
