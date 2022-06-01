<?php
use Brain\Monkey\Functions;
class WCVendors_Pro_Vendor_Controller_Test extends WCVendorsTestCase {
	public function test_save_social_media_settings() {
		$controller = new WCVendors_Pro_Vendor_Controller( 'wc-vendor-pro', '1.0', true );

		// Test if no nonce is passed.
		$this->assertFalse( $controller->save_social_media_settings( 1 ) );

		// Test verifying nonce failed if no nonce is passed or wrong nonce.
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
		$this->assertFalse( $controller->save_social_media_settings( 1 ) );

		// Test if nonce is verified.
		$_POST['_wcv-save_store_settings'] = 'any_nonce';

		// Ensure delete user meta if no value is passed to setting fields.
		Functions\expect( 'sanitize_text_field' )
			->atLeast()
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturnFirstArg();
		Functions\expect( 'delete_user_meta' )
			->atLeast()
			->once()
			->with( Mockery::type( 'int' ), Mockery::type( 'string' ) )
			->andReturn( true );
		$this->assertNull( $controller->save_social_media_settings( 1 ) );

		// Test update user meta if we have valid value.
		$_POST['_wcv_twitter_username'] = 'dinhtungdu';
		Functions\expect( 'update_user_meta' )
			->atLeast()
			->once()
			->with( Mockery::type( 'int' ), Mockery::type( 'string' ), Mockery::type( 'string' ) )
			->andReturn( true );
		$this->assertNull( $controller->save_social_media_settings( 1 ) );
	}
}
