<?php
/**
 * Class WCVendors_Pro_Product_Report_Controller
 *
 * @author  WC Vendors
 * @package WCVendors_Pro
 */

/**
 * Class WCVendors_Pro_Product_Report_Controller
 */
class WCVendors_Pro_Product_Report_Controller {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'process_report_form_submit' ) );
		add_action( 'wcvendors_admin_field_report_product_reason', array( $this, 'generate_reason_field_html' ), 10, 1 );
		add_action( 'admin_notices', array( $this, 'report_product_admin_notices' ) );
		add_action( 'init', array( $this, 'check_db_table' ) );
	}

	public function process_report_form_submit() {
		if ( ! isset( $_POST['_wcv_report_product_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wcv_report_product_nonce'], 'wcv-report-product' ) ) {
			return;
		}

		$product_id  = get_the_ID();
		$reporter_id = get_current_user_id();
		$vendor_id   = WCV_Vendors::get_vendor_from_product( $product_id );

		if ( $vendor_id == $reporter_id ) {
			return;
		}

		if ( ! WCVendors_Pro_Report_Product_Form::maybe_report( $product_id ) ) {
			return;
		}

		$report_reason = sanitize_text_field( $_POST['_wcv_report_product_reason'] );
		$report_notes  = sanitize_textarea_field( $_POST['_wcv_report_product_notes'] );
		$report_data   = array(
			'product_id'    => $product_id,
			'reporter_id'   => $reporter_id,
			'vendor_id'     => $vendor_id,
			'report_reason' => $report_reason,
			'report_notes'  => $report_notes,
			'report_status' => 'pending',
		);

		$report_id = $this->insert_report_data( $report_data );

		if ( $report_id ) {
			$this->send_report_mail_to_admin( $report_data );
			wc_add_notice( __( 'Your report has been submitted.', 'wcvendors-pro' ), 'success' );
			$product_url = get_permalink( $product_id );
			wp_safe_redirect( $product_url, 302 );
			exit;
		}

	}

	/**
	 * Insert report data
	 *
	 * @param array $report_data Report data.
	 *
	 * @return bool|int Report ID
	 */
	private function insert_report_data( $report_data ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wcv_product_report';

		$wpdb->insert(
			$table_name,
			$report_data,
			array(
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
			)
		);

		return $wpdb->insert_id;
	}

	/**
	 * Send report mail to admin
	 *
	 * @param array $report_data Report data.
	 */
	private function send_report_mail_to_admin( $report_data ) {
		$admin_email = get_option( 'admin_email' );
		$product     = wc_get_product( $report_data['product_id'] );
		$customer    = get_user_by( 'id', $report_data['reporter_id'] );
		$vendor      = get_user_by( 'id', $report_data['vendor_id'] );
		/* translators: %1$s: product title,*/
		$subject = sprintf( __( 'Product Report - %s <br>', 'wcvendors-pro' ), $product->get_title() );
		/* translators: %1$s: product ID,*/
		$message = sprintf( __( 'Product ID: %s<br>', 'wcvendors-pro' ), $report_data['product_id'] );
		/* translators: %1$s: Reporter,*/
		$message .= sprintf( __( 'Reporter: %s<br>', 'wcvendors-pro' ), $customer->display_name );
		/* translators: %1$s: Vendor,*/
		$message .= sprintf( __( 'Vendor: %s<br>', 'wcvendors-pro' ), $vendor->display_name );
		/* translators: %1$s: Report reason,*/
		$message .= sprintf( __( 'Report Reason: %s<br>', 'wcvendors-pro' ), $report_data['report_reason'] );
		/* translators: %1$s: Report notes,*/
		$message .= sprintf( __( 'Report Notes: %s<br>', 'wcvendors-pro' ), $report_data['report_notes'] );

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);

		$mail_sent = wp_mail( $admin_email, $subject, $message, $headers );

		return $mail_sent;
	}

	/**
	 * Generate reason field html
	 *
	 * @param array $field Field data.
	 */
	public function generate_reason_field_html( $field ) {

		if ( 'report_product_reason' !== $field['type'] ) {
			return;
		}

		$reasons = get_option( 'wcvendors_pro_report_product_reasons', array() );

		ob_start();

		include WCV_PRO_ABSPATH_ADMIN . 'settings/partials/html-report-product-reasons.php';

		echo ob_get_clean();
	}

	/**
	 * Admin notices respond success
	 */
	public function report_product_admin_notices() {

		$respond_nonce = isset( $_GET['respond_nonce'] ) ? $_GET['respond_nonce'] : '';
		if ( ! wp_verify_nonce( $respond_nonce, 'wcv-report-respond-status_nonce' ) ) {
			return;
		}

		$respond_status = sanitize_text_field( $_GET['respond_status'] );

		$message       = 'success' === $respond_status ? __( 'Your respond has been submitted.', 'wcvendors-pro' ) : __( 'Failed submitting your respond.', 'wcvendors-pro' );
		$respond_class = 'success' === $respond_status ? 'success' : 'error';

		echo sprintf(
			'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
			esc_html( $respond_class ),
			esc_html( $message ),
		);
	}

	/**
	 * Check if db table exists
	 */
	public function check_db_table() {
		global $wpdb;
		$table_name     = $wpdb->prefix . 'wcv_product_report';
		$show_table_sql = $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name );
		if ( $wpdb->get_var( $show_table_sql ) !== $table_name ) {
			wcv_create_product_report_table();
		}
	}
}
