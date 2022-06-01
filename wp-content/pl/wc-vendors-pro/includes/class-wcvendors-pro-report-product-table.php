<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * WCVendors_Pro_Report_Product_Table
 *
 * @author     WCVendors
 */
class WCVendors_Pro_Report_Product_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Report', 'wcvendors-pro' ),
				'plural'   => __( 'Reports', 'wcvendors-pro' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Get the table data
	 *
	 * @param  array $per_page Number of items to show per page.
	 * @param  array $page_number Current page number.
	 * @return array
	 */
	public function table_data( $per_page = 10, $page_number = 1 ) {
		global $wpdb;

		$sql      = "SELECT * FROM {$wpdb->prefix}wcv_product_report";
		$order_by = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'report_date';
		$order    = isset( $_REQUEST['order'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'DESC';
		if ( ! empty( $order_by ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $order_by );
			$sql .= ' ' . esc_sql( $order );
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql );

		return $result;
	}

	/**
	 * Get the total number of reports
	 *
	 * @return int
	 */
	public function total_reports() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wcv_product_report';
		$sql        = "SELECT COUNT(*) FROM {$table_name}";
		$count      = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $count;
	}

	/**
	 * Get the columns
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'               => '<input type="checkbox" />',
			'product_id'       => __( 'Product', 'wcvendors-pro' ),
			'reporter_id'      => __( 'Reporter', 'wcvendors-pro' ),
			'report_vendor_id' => __( 'Vendor', 'wcvendors-pro' ),
			'report_reason'    => __( 'Reason', 'wcvendors-pro' ),
			'report_notes'     => __( 'Notes', 'wcvendors-pro' ),
			'status'           => __( 'Status', 'wcvendors-pro' ),
			'report_date'      => __( 'Date', 'wcvendors-pro' ),
		);
		return $columns;
	}

	/**
	 * Get the sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'product_id'       => array( 'product_id', false ),
			'reporter_id'      => array( 'reporter_id', false ),
			'report_vendor_id' => array( 'vendor_id', false ),
			'report_reason'    => array( 'report_reason', false ),
			'report_date'      => array( 'report_date', true ),
		);
		return $sortable_columns;
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = $this->get_hidden_columns();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		$this->process_action();
		$total_items = $this->total_reports();
		$per_page    = $this->get_items_per_page( 'report_product_per_page', 10 );
		$this->items = $this->table_data( $per_page, $this->get_pagenum() );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		return array(
			'report_id',
			'report_vendor_id',
			'report_date',
		);
	}

	/**
	 * Message to be displayed when there are no items
	 */
	public function no_items() {
		esc_html_e( 'No reports found.', 'wcvendors-pro' );
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param object $item Item data.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="report_ids[]" value="%s" />',
			$item->report_id
		);
	}

	/**
	 * Method for name column
	 *
	 * @param object $item Item data.
	 *
	 * @return string
	 */
	public function column_product_id( $item ) {
		$action_nonce = wp_create_nonce( 'wcv-report-product-action-nonce' );
		$product_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_edit_post_link( $item->product_id ) ),
			get_the_title( $item->product_id )
		);
		$page         = ( isset( $_REQUEST['page'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
		$actions      = array(
			'accept'  => sprintf(
				'<a href="?page=%s&action=%s&report_id=%s&_wpnonce=%s">Accept</a>',
				esc_attr( $page ),
				'accept',
				absint( $item->report_id ),
				esc_attr( $action_nonce )
			),
			'ignore'  => sprintf(
				'<a href="?page=%s&action=%s&report_id=%s&_wpnonce=%s">Ignore</a>',
				esc_attr( $page ),
				'ignore',
				absint( $item->report_id ),
				esc_attr( $action_nonce )
			),
			'ajust'   => sprintf(
				'<a href="?page=%s&action=%s&report_id=%s&_wpnonce=%s">Adjust</a>',
				esc_attr( $page ),
				'adjust',
				absint( $item->report_id ),
				esc_attr( $action_nonce )
			),
			'respond' => sprintf(
				'<a href="#" class="wcv-report-respond" data-popup="%s" data-report-id="%s">Respond</a>',
				esc_attr( $this->respond_popup( $item ) ),
				absint( $item->report_id )
			),
		);
		switch ( $item->report_status ) {
			case 'closed':
				unset( $actions['accept'] );
				unset( $actions['ignore'] );
				break;
		}
		return $product_link . $this->row_actions( $actions );
	}

	/**
	 * Column default.
	 *
	 * @param object $item Item data.
	 * @param string $column_name Column name.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'reporter_id':
				$reporter = get_user_by( 'id', $item->reporter_id );
				if ( $reporter ) {
					return '<a href="' . get_edit_user_link( $item->reporter_id ) . '">' . $reporter->display_name . '</a>';
				}
				return '-';
			case 'report_vendor_id':
				$vendor = get_user_by( 'id', $item->vendor_id );
				if ( $vendor ) {
					return '<a href="' . get_edit_user_link( $item->vendor_id ) . '">' . $vendor->display_name . '</a>';
				}
				return '-';
			case 'report_reason':
				return $item->report_reason;
			case 'report_notes':
				return $item->report_notes;
			case 'report_date':
				return $item->report_date;
			case 'status':
				return $item->report_status;
			default:
				return '-';
		}
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'accept'    => __( 'Accept', 'wcvendors-pro' ),
			'ignore'    => __( 'Ignore', 'wcvendors-pro' ),
			'ajustment' => __( 'Adjustment', 'wcvendors-pro' ),
		);
		return $actions;
	}


	/**
	 * Handle bulk actions
	 */
	public function process_bulk_action() {
		if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
			return;
		}

		if ( ! isset( $_POST['report_ids'] ) ) {
			return;
		}
		if ( ! is_array( $_POST['report_ids'] ) ) {
			return;
		}

		$report_ids = array_map( 'absint', $_POST['report_ids'] );
		$action     = $this->current_action();

		switch ( $action ) {
			case 'accept':
				foreach ( $report_ids as $report_id ) {
					$this->accept_report( $report_id );
				}
				break;
			case 'ignore':
				foreach ( $report_ids as $report_id ) {
					$this->ignore_report( $report_id );
				}
				break;
			case 'ajustment':
				foreach ( $report_ids as $report_id ) {
					$this->adjust_report( $report_id );
				}
				break;
		}
	}

	/**
	 * Proces action
	 */
	public function process_action() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) );

		if ( ! isset( $_REQUEST['report_id'] ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wcv-report-product-action-nonce' ) ) {
			return;
		}

		$report_id = absint( wp_unslash( $_REQUEST['report_id'] ) );

		switch ( $action ) {
			case 'accept':
				$this->accept_report( $report_id );
				break;
			case 'ignore':
				$this->ignore_report( $report_id );
				break;
			case 'adjust':
				$this->adjust_report( $report_id );
				break;
			case 'respond':
				$this->respond_report( $report_id );
				break;
		}
	}

	/**
	 * Get report data
	 *
	 * @param int $report_id Report ID.
	 */

	public function get_report_data( $report_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wcv_product_report';
		$sql        = "SELECT * FROM $table_name WHERE report_id = %d";
		$sql        = $wpdb->prepare( $sql, $report_id );
		$report     = $wpdb->get_row( $sql );

		if ( ! $report ) {
			return false;
		}

		return $report;
	}

	/**
	 * Update report status
	 *
	 * @param int    $report_id Report ID.
	 * @param string $status Status.
	 */
	public function update_report_status( $report_id, $status ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wcv_product_report';
		$sql        = "UPDATE $table_name SET report_status = %s WHERE report_id = %d";
		$sql        = $wpdb->prepare( $sql, $status, $report_id );
		$wpdb->query( $sql );
	}

	/**
	 * Convert report data to email message
	 *
	 * @param object $report_data Report data.
	 */
	public function report_data_to_email( $report_data ) {

		$product  = wc_get_product( $report_data->product_id );
		$message  = '<br>';
		$message .= sprintf( __( 'Report Date: %s', 'wcvendors-pro' ), $report_data->report_date ) . '<br>';
		$message .= sprintf( __( 'Report Reason: %s', 'wcvendors-pro' ), $report_data->report_reason ) . '<br>';
		$message .= sprintf( __( 'Report Notes: %s', 'wcvendors-pro' ), $report_data->report_notes ) . '<br>';
		$message .= sprintf( __( 'Report Status: %s', 'wcvendors-pro' ), $report_data->report_status ) . '<br>';
		$message .= sprintf( __( 'Report Product: %s', 'wcvendors-pro' ), $product->get_title() ) . '<br>';
		return $message;
	}


	/**
	 * Accept report
	 *
	 * @param int $report_id Report ID.
	 */
	public function accept_report( $report_id ) {
		$report = $this->get_report_data( $report_id );
		if ( $report ) {
			$this->update_report_status( $report_id, 'open' );
			$message      = __( 'Your product has been reported and deleted', 'wcvendors-pro' );
			$subject      = __( 'Product Report', 'wcvendors-pro' );
			$vendor_email = get_user_by( 'id', $report->vendor_id )->user_email;
			$sent         = $this->send_notification( $report, $vendor_email, $subject, $message );
			if ( $sent ) {
				$this->delete_product( $report->product_id );
			}
		}
	}

	/**
	 * Ignore report
	 *
	 * @param int $report_id Report ID.
	 */
	public function ignore_report( $report_id ) {
		$report = $this->get_report_data( $report_id );
		if ( $report ) {
			$this->update_report_status( $report_id, 'closed' );
		}
	}

	/**
	 * Adjust report
	 *
	 * @param int $report_id Report ID.
	 */
	public function adjust_report( $report_id ) {
		$report = $this->get_report_data( $report_id );
		if ( $report ) {
			$this->update_report_status( $report_id, 'open' );
			$message      = __( 'Your product has been reported and need to be ajust', 'wcvendors-pro' );
			$subject      = __( 'Product Report', 'wcvendors-pro' );
			$vendor_email = get_user_by( 'id', $report->vendor_id )->user_email;
			$sent         = $this->send_notification( $report, $vendor_email, $subject, $message );
			if ( $sent ) {
				$this->set_hidden_product( $report->product_id );
			}
		}
	}

	/**
	 * Set product to hidden
	 *
	 * @param int $product_id Product ID.
	 */
	public function set_hidden_product( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			$product->set_catalog_visibility( 'hidden' );
			$product->save();
		}
	}

	/**
	 * Detete product
	 *
	 * @param int $product_id The product id.
	 */
	public function delete_product( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			$product->delete( false );
		}
	}

	/**
	 * Respond report popup data
	 *
	 * @param object $report_data Report data.
	 */
	public function respond_popup( $report_data ) {
		$vendor   = get_user_by( 'id', $report_data->vendor_id );
		$reporter = get_user_by( 'id', $report_data->reporter_id );
		$product  = wc_get_product( $report_data->product_id );
		ob_start();
		wc_get_template(
			'report-product-popup.php',
			array(
				'report_data' => $report_data,
				'vendor'      => $vendor,
				'reporter'    => $reporter,
				'product'     => $product,
			),
			'wc-vendors/report-product/',
			WCV_PRO_ABSPATH_TEMPLATES . '/report-product/'
		);
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Process report respond form
	 */
	public function respond_report() {
		global $wpdb;
		$report_id       = isset( $_POST['report_id'] ) ? wp_unslash( sanitize_text_field( $_POST['report_id'] ) ) : false;

		$respond_success = false;
		if ( ! $report_id ) {
			return;
		}

		$report_data = $this->get_report_data( $report_id );

		if ( ! $report_data ) {
			return;
		}

		$respond_to     = isset( $_POST['respond_to'] ) ? wp_unslash( sanitize_text_field( $_POST['respond_to'] ) ) : '';
		$respond_note   = isset( $_POST['respond_note'] ) ? wp_unslash( sanitize_textarea_field( $_POST['respond_note'] ) ) : '';
		$delete_product = isset( $_POST['delete_product'] ) ? wp_unslash( sanitize_textarea_field( $_POST['delete_product'] ) ) : 'no';
		$report_status  = wc_string_to_bool( $delete_product ) ? 'closed' : 'pending';
		$report_data    = $this->get_report_data( $report_id );
		$table_name     = $wpdb->prefix . 'wcv_product_report';
		$subject        = __( 'Report Respond', 'wcvendors-pro' );
		$message        = sprintf( __( 'Respond notes: %s', 'wcvendors-pro' ), $respond_note );

		if ( 'vendor' === $respond_to ) {

			$where = array(
				'report_id' => $report_id,
			);
			$data  = array(
				'report_status'        => $report_status,
				'report_respond_notes' => $respond_note,
				'respond_sent_to'      => $report_data->vendor_id,
			);

			$updated = $wpdb->update( $table_name, $data, $where );
			if ( $updated ) {
				$vendor_email = get_user_by( 'id', $report_data->vendor_id )->user_email;

				$sent = $this->send_notification( $report_data, $vendor_email, $subject, $message );

				if ( $sent ) {
					$respond_success = true;
					if ( wc_string_to_bool( $delete_product ) ) {
						$this->delete_product( $report_data->product_id );
					}
				}
			}
		}

		if ( 'reporter' === $respond_to ) {

			$where = array(
				'report_id' => $report_id,
			);
			$data  = array(
				'report_status'        => 'closed',
				'report_respond_notes' => $respond_note,
				'respond_sent_to'      => $report_data->reporter_id,
			);

			$updated = $wpdb->update( $table_name, $data, $where );
			if ( $updated ) {
				$reporter_email = get_user_by( 'id', $report_data->reporter_id )->user_email;
				$sent           = $this->send_notification( $report_data, $reporter_email, $subject, $message );

				if ( $sent ) {
					$respond_success = true;
					if ( wc_string_to_bool( $delete_product ) ) {
						$this->delete_product( $report_data->product_id );
					}
				}
			}
		}

		$respond_status_nonce = wp_create_nonce( 'wcv-report-respond-status_nonce' );
		$respond_status       = $respond_success ? 'success' : 'failed';
		$redirect_uri         = add_query_arg(
			array(
				'respond_status' => $respond_status,
				'respond_nonce'  => $respond_status_nonce,
			),
		);
		wp_safe_redirect( $redirect_uri );
		exit;
	}

	/**
	 * Send notification
	 *
	 * @param  object $report_data The attachments of the message.
	 * @param  string $email   The email to send the message to.
	 * @param  string $subject The subject of the message.
	 * @param  string $message The message to send.
	 */
	public function send_notification( $report_data, $email, $subject, $message ) {
		$message .= $this->report_data_to_email( $report_data );
		$header   = array(
			'Content-Type: text/html; charset=UTF-8',
		);
		$sent     = wp_mail( $email, $subject, $message, $header );
		if ( $sent ) {
			return true;
		}
		return false;
	}

}
