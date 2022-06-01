<?php
use Brain\Monkey\Functions;
class WCVendors_Pro_Admin_Test extends WCVendorsTestCase {
	protected function setUp(): void {
		parent::setUp();
		$_POST = array();
	}

	public function test_disable_vendor_edit_product() {
		Functions\expect( 'woocommerce_wp_checkbox' )
			->once();

		Functions\when( 'wcv_get_vendor_name' )->justReturn( 'vendor' );

		$admin = new WCVendors_Pro_Admin( new stdClass(), '1.0', false );

		$this->expectOutputRegex( '/.*options_group.*/', $admin->disable_vendor_edit_product() );
	}

	public function test_save_disable_vendor_edit_product_without_nonce() {
		$admin = new WCVendors_Pro_Admin( new stdClass(), '1.0', false );

		$this->assertFalse( $admin->save_disable_vendor_edit_product( 1 ) );
	}

	public function test_save_disable_vendor_edit_product_failed() {
		$admin = new WCVendors_Pro_Admin( new stdClass(), '1.0', false );

		Functions\expect( 'wp_verify_nonce' )
			->atLeast()
			->once()
			->with( Mockery::type( 'string' ), Mockery::type( 'string' ) )
			->andReturn( 1 );

		Functions\expect( 'sanitize_key' )
			->atLeast()
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturnFirstArg();

		Functions\expect( 'wp_unslash' )
			->atLeast()
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturnFirstArg();

		Functions\expect( 'delete_post_meta' )
			->atLeast()
			->once()
			->with( Mockery::type( 'int' ), Mockery::type( 'string' ) );

		$_POST['_wpnonce'] = 'any_nonce';
		$this->assertFalse( $admin->save_disable_vendor_edit_product( 1 ) );

		$_POST['_disable_vendor_edit'] = '';
		$this->assertFalse( $admin->save_disable_vendor_edit_product( 1 ) );

		$_POST['_disable_vendor_edit'] = 'no';
		$this->assertFalse( $admin->save_disable_vendor_edit_product( 1 ) );
	}

	public function test_save_disable_vendor_edit_product_success() {
		$admin = new WCVendors_Pro_Admin( new stdClass(), '1.0', false );

		Functions\expect( 'wp_verify_nonce' )
			->atLeast()
			->once()
			->with( Mockery::type( 'string' ), Mockery::type( 'string' ) )
			->andReturn( 1 );

		Functions\expect( 'sanitize_key' )
			->atLeast()
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturnFirstArg();

		Functions\expect( 'wp_unslash' )
			->atLeast()
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturnFirstArg();

		Functions\expect( 'update_post_meta' )
			->atLeast()
			->once()
			->with( Mockery::type( 'int' ), Mockery::type( 'string' ), 'yes' );

		$_POST['_wpnonce']             = 'any_nonce';
		$_POST['_disable_vendor_edit'] = 'yes';

		$this->assertNull( $admin->save_disable_vendor_edit_product( 1 ) );
	}
}
