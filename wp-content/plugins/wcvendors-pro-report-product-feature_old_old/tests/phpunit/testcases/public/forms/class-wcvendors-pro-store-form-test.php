<?php
use Brain\Monkey\Functions;
class WCVendors_Pro_Store_Form_Test extends WCVendorsTestCase {
	public function test_render_social_media_settings_disabled() {
		Functions\when( 'get_current_user_id' )
			->justReturn( 1 );

		Functions\expect( 'get_option' )
			->atLeast()->once()
			->andReturn( 'yes' );

		Functions\expect( 'get_user_meta' )
			->never();

		$this->assertNull( WCVendors_Pro_Store_Form::render_social_media_settings() );
	}

	public function test_render_social_media_settings_enabled() {
		Functions\when( 'get_current_user_id' )
			->justReturn( 1 );

		Functions\expect( 'get_option' )
			->atLeast()->once()
			->andReturn( 'no' );

		Functions\expect( 'get_user_meta' )
			->atLeast()->once()
			->with( Mockery::type( 'int' ), Mockery::type( 'string' ), true );

		$form_helper = Mockery::mock( 'alias:WCVendors_Pro_Form_Helper' )->makePartial();
		$form_helper->shouldReceive( 'input' )->atLeast()->once()->andReturn( null );

		$this->assertNull( WCVendors_Pro_Store_Form::render_social_media_settings() );
	}
}
