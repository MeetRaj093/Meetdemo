<?php
/**
 * Ready to use test case which set up Brain Monkey.
 *
 * @package WCVendors_Pro
 */

use Brain\Monkey;

/**
 * Base test case for WC Vendors Component.
 */
class WCVendorsTestCase extends \PHPUnit\Framework\TestCase {

	/**
	 * Set up test case.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		// A few common passthrough.
		Monkey\Functions\stubs(
			array(
				'__',
				'_e',
				'_n',
				'plugin_dir_url',
				'plugin_dir_path',
			)
		);
	}

	/**
	 * Tear down test case.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}

