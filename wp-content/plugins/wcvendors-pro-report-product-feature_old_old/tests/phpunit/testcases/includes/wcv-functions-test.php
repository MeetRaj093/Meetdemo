<?php
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

class WCV_Functions_Test extends WCVendorsTestCase {
	public function test_wcv_get_social_media_settings() {

		Filters\expectApplied( 'wcvendors_social_media_settings' )
			->once()
			->with( Mockery::type( 'array' ) )
			->andReturnFirstArg();

		$settings = wcv_get_social_media_settings();

		$this->assertIsArray( $settings );
		$this->assertNotEmpty( $settings );
		$this->assertArrayHasKey( 'twitter', $settings );
		$this->assertArrayHasKey( 'instagram', $settings );
		$this->assertArrayHasKey( 'facebook', $settings );
		$this->assertArrayHasKey( 'linkedin', $settings );
		$this->assertArrayHasKey( 'youtube', $settings );
		$this->assertArrayHasKey( 'pinterest', $settings );
		$this->assertArrayHasKey( 'snapchat', $settings );
		$this->assertArrayHasKey( 'telegram', $settings );

		foreach ( $settings as $setting ) {
			$this->assertArrayHasKey( 'id', $setting );
			$this->assertArrayHasKey( 'label', $setting );
			$this->assertArrayHasKey( 'type', $setting );
		}
	}

	public function test_wcv_format_store_social_icons_render_nothing() {
		Functions\expect( 'get_user_meta' )
			->atLeast()->once()
			->with( Mockery::type( 'int' ), Mockery::type( 'string' ), true )
			->andReturn( false );
		$this->assertNull( wcv_format_store_social_icons( 1 ) );
	}

	public function test_wcv_format_store_social_icons_render_links() {
		Functions\expect( 'get_user_meta' )
			->atLeast()->once()
			->with( Mockery::type( 'int' ), Mockery::type( 'string' ), true )
			->andReturn( 'social_profile' );
		define( 'WCV_PRO_PUBLIC_ASSETS_URL', 'https://wcvendors.com' );
		$output = wcv_format_store_social_icons( 1, 'sm', array( 'twitter' ) );
		$this->assertIsString( $output );
		$this->assertStringContainsString( 'social-icons', $output );
		$this->assertStringContainsString( 'social_profile', $output );
		$this->assertStringContainsString( 'wcv-icon-sm', $output );
		$this->assertStringNotContainsString( 'twitter', $output );
	}
}
