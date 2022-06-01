<?php
/**
 * Settings: Product Reports
 *
 * @package     WCVendors_Pro
 * @author      WC Vendors
 */

/**
 * Class WCVendors_Pro_Settings_Product_Reports
 */
class WCvendors_Pro_Setting_Product_Reports extends WCVendors_Settings_Page {

		/**
		 * Constructor.
		 */
	public function __construct() {
		$this->id    = 'product_reports';
		$this->label = __( 'Product Reports', 'wcvendors-pro' );

		parent::__construct();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'' => __( 'General', 'wcvendors-pro' ),
		);

		return apply_filters( 'wcvendors_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @param string $current_section Current section name.
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {

		if ( '' === $current_section ) {

			$settings = apply_filters(
				'wcvendors_pro_settings_product_reports_general',
				array(
					array(
						'title' => __( 'General', 'wcvendors-pro' ),
						'type'  => 'title',
						'desc'  => 'General options for the report product system.',
						'id'    => 'product_reports_options',
					),
					array(
						'type' => 'product_reports_reason',
						'id'   => 'wcvendors_pro_product_reports_reasons',
					),
					array(
						'title'   => __( 'Enable new report email notification', 'wcvendors-pro' ),
						'desc'    => __( 'Send an email to the admin when a new report is submitted.', 'wcvendors-pro' ),
						'id'      => 'wcvendors_pro_product_reports_email_notification',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'product_reports_options_end',
					),
				)
			);
			return apply_filters( 'wcvendors_get_settings_' . $this->id, $settings, $current_section );
		}
	}
}
return new WCvendors_Pro_Setting_Product_Reports();

