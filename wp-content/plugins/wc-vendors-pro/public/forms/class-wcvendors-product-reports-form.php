<?php
/**
 * Output the report product button and popup
 *
 * @package  WCVendors_Pro
 * @author   WC Vendors
 * @since    1.8.3
 * @version  1.8.3
 */

/**
 * Class WCVendors_Pro_Product_Reports_Form
 */
class WCVendors_Pro_Product_Reports_Form {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wcvendors_product_meta_start', array( $this, 'output' ) );
	}

	/**
	 * Add report button and popup under product meta.
	 */
	public function product_reports_form_content() {
		global $post;
		if ( ! is_user_logged_in() ) {
			return;
		}
		$post_author = get_post_field( 'post_author', $post->ID );
		if ( ! self::maybe_report( $post->ID ) || $post_author == get_current_user_id() ) {
			return;
		}

		$vendor_id    = WCV_Vendors::get_vendor_from_product( $post->ID );
		$reporter_id  = get_current_user_id();
		$button_label = apply_filters( 'wcvendors_product_reports_button_label', __( 'Report item', 'wcvendors-pro' ) );
		$popup_title  = apply_filters( 'wcvendors_product_reports_popup_title', __( 'Report item', 'wcvendors-pro' ) ) . ': ' . get_the_title( $post->ID );

		if ( $vendor_id === $reporter_id ) {
			return;
		}

		wc_get_template(
			'product-reports.php',
			array(
				'button_label' => $button_label,
				'popup_title'  => $popup_title,
			),
			'wc-vendors/product/',
			WCV_PRO_ABSPATH_TEMPLATES . '/product/'
		);
	}

	/**
	 * Output the report product reason field
	 */
	public static function report_reason_field() {
		$default_reason = apply_filters(
			'wcvendors_product_reports_default_reason',
			array(
				__( 'Offensive', 'wcvendors-pro' ),
				__( 'Against copyright', 'wcvendors-pro' ),
				__( 'Inappropriate', 'wcvendors-pro' ),
				__( 'Prohibited', 'wcvendors-pro' ),
				__( 'Other', 'wcvendors-pro' ),
			)
		);

		$user_reason   = get_option( 'wcvendors_pro_product_reports_reasons', array() );
		$user_reason   = array_merge( $user_reason, $default_reason );
		$report_reason = apply_filters(
			'wcvendors_product_reports_reason',
			$user_reason
		);
		$report_reason = array_combine( $report_reason, $report_reason );

		WCVendors_Pro_Form_Helper::select(
			apply_filters(
				'wcvendors_product_reports_reason_field',
				array(
					'id'                => '_wcv_product_reports_reason',
					'label'             => __( 'Reason', 'wcvendors-pro' ),
					'options'           => $report_reason,
					'value'             => '',
					'wrapper_tag'       => 'div',
					'wrapper_class'     => 'form-row form-row-wide',
					'field_class'       => 'select2',
					'show_option_none'  => __( 'Select a reason', 'wcvendors-pro' ),
					'custom_attributes' => array(
						'data-placeholder' => __( 'Select a reason', 'wcvendors-pro' ),
						'data-allow-clear' => 'true',
					),
				)
			)
		);
	}

	/**
	 * Output the report product notes field
	 */
	public static function report_note_field() {
		WCVendors_Pro_Form_Helper::textarea(
			apply_filters(
				'wcvendors_product_reports_note_field',
				array(
					'id'            => '_wcv_product_reports_notes',
					'label'         => __( 'Notes', 'wcvendors-pro' ),
					'placeholder'   => __( 'Notes / other reason', 'wcvendors-pro' ),
					'value'         => '',
					'wrapper_tag'   => 'div',
					'wrapper_class' => 'control-group',
					'field_class'   => 'control',
				)
			)
		);
	}

	/**
	 * Output the report product nonce field
	 */
	public static function product_reports_nonce_field() {
		WCVendors_Pro_Form_Helper::input(
			apply_filters(
				'wcvendors_product_reports_nonce_field',
				array(
					'id'    => '_wcv_product_reports_nonce',
					'value' => wp_create_nonce( 'wcv-product-reports' ),
					'type'  => 'hidden',
				)
			)
		);
	}

	/**
	 * Output the report product submit button
	 */
	public static function product_reports_submit_button() {
		WCVendors_Pro_Form_Helper::submit(
			apply_filters(
				'wcvendors_product_reports_submit_button',
				array(
					'id'            => '_wcv_product_reports_submit',
					'label'         => __( 'Submit report', 'wcvendors-pro' ),
					'type'          => 'submit',
					'value'         => __( 'Submit report', 'wcvendors-pro' ),
					'wrapper_tag'   => 'div',
					'wrapper_class' => 'form-row form-row-wide',
					'field_class'   => '',
				)
			)
		);
	}

	/**
	 * Check if the product is reported by the current user
	 *
	 * @param  int $product_id Product id.
	 */
	public static function maybe_report( $product_id ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wcv_product_report';

		$sql = "SELECT * FROM $table_name WHERE product_id = %d AND reporter_id = %d";

		$result = $wpdb->get_row( $wpdb->prepare( $sql, $product_id, get_current_user_id() ) );

		if ( $result ) {
			return false;
		}

		return true;
	}
}
