<?php
use Brain\Monkey\Functions;
class WCVendors_Pro_Admin_Vendor_Controller_Test extends WCVendorsTestCase {
	public function test_get_admin_social_media_settings() {
		$controller     = new WCVendors_Pro_Admin_Vendor_Controller( 'wc-vendors-pro', '1.0', false );
		$admin_settings = $controller->get_admin_social_media_settings();
		$this->assertIsArray( $admin_settings );
		$this->assertArrayHasKey( 'title', $admin_settings );
		$this->assertArrayHasKey( 'fields', $admin_settings );
		$this->assertArrayHasKey( 'label', array_values( $admin_settings['fields'] )[0] );
		$this->assertArrayHasKey( 'description', array_values( $admin_settings['fields'] )[0] );
	}
}
