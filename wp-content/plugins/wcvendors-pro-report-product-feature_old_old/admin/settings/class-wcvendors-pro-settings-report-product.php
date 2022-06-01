<?php
/**
 * Settings: Report Product
 *
 * @package     WCVendors_Pro
 * @author      WC Vendors
 */

/**
 * Class WCVendors_Pro_Settings_Report_Product
 */
class WCvendors_Pro_Setting_Report_Product extends WCVendors_Settings_Page {

		/**
		 * Constructor.
		 */
	public function __construct() {
		$this->id    = 'report_product';
		$this->label = __( 'Report Product', 'wcvendors-pro' );

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
				'wcvendors_pro_settings_report_product_general',
				array(
					array(
						'title' => __( 'General', 'wcvendors-pro' ),
						'type'  => 'title',
						'desc'  => 'General options for the report product system.',
						'id'    => 'report_product_options',
					),
					array(
						'type' => 'report_product_reason',
						'id'   => 'wcvendors_pro_report_product_reasons',
					),
					array(
						'title'   => __( 'Enable new report email notification', 'wcvendors-pro' ),
						'desc'    => __( 'Send an email to the admin when a new report is submitted.', 'wcvendors-pro' ),
						'id'      => 'wcvendors_pro_report_product_email_notification',
						'default' => 'yes',
						'type'    => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => 'report_product_options_end',
					),
				)
			);
			return apply_filters( 'wcvendors_get_settings_' . $this->id, $settings, $current_section );
		}
	}
}
return new WCvendors_Pro_Setting_Report_Product();

