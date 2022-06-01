<?php

/**
 * Vendor Product Import/Export Tools
 *
 * This is the controller class for all front end actions
 *
 * @package    WCVendors_Pro\Dashboard\Product
 * @author     Jamie Madden <support@wcvendors.com>
 */
class WCVendors_Pro_Import_Export {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $wcvendors_pro The ID of this plugin.
	 */
	private $wcvendors_pro;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Is the plugin in debug mode
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      bool $debug plugin is in debug mode
	 */
	private $debug;

	/**
	 * Is the plugin base directory
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $base_dir string path for the plugin directory
	 */
	private $base_dir;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $wcvendors_pro The name of the plugin.
	 * @param      string $version       The version of this plugin.
	 */
	public function __construct( $wcvendors_pro, $version, $debug ) {

		$this->wcvendors_pro = $wcvendors_pro;
		$this->version       = $version;
		$this->debug         = $debug;
		$this->base_dir      = plugin_dir_path( dirname( __FILE__ ) );
		$this->includes_dir  = $this->base_dir . 'public/includes/import-export/';

	}

	/**
	 * Add the import and export sub pages under products
	 */
	public function add_subpages( $pages ) {

		// Import Page.
		if ( wc_string_to_bool( get_option( 'wcvendors_capability_products_import', false ) ) ) {
			// This page doesn't need a template.
			$pages['import'] = array(
				'parent' => 'product',
				'slug'   => 'import',
			);
		}

		// Export page.
		if ( wc_string_to_bool( get_option( 'wcvendors_capability_products_export', false ) ) ) {
			include_once 'includes/import-export/export/class-wcv-product-csv-exporter.php';
			$pages['export'] = array(
				'parent'        => 'product',
				'slug'          => 'export',
				'base_dir'      => $this->base_dir . '/templates/dashboard/product/',
				'template_name' => 'exporter',
				'args'          => array(
					'exporter' => new WCV_Product_CSV_Exporter(),
				),
			);
		}

		return $pages;

	}

	/**
	 * Add Import custom page to the vendor navigation
	 *
	 * @since 1.8.0
	 */
	public function add_import_page( $object, $object_id, $template, $custom ) {

		if ( 'product' === $object && 'import' === $custom ) {
			include_once $this->includes_dir . 'import/class-wcv-product-csv-importer.php';
			include_once $this->includes_dir . 'importers/class-wcv-product-csv-importer-controller.php';
			$import_controller = new \WCV_Product_CSV_Importer_Controller();
			$import_controller->dispatch();
		}

	}

	/**
	 * Handle the import steps sequence for multi-step form. This comes from the WooCommerce product importer
	 *
	 * @since 1.8.0
	 */
	public function handle_steps() {

		$posted_data = wp_unslash( $_POST ); // phpcs:ignore

		include_once $this->includes_dir . 'importers/class-wcv-product-csv-importer-controller.php';
		$import_controller = new \WCV_Product_CSV_Importer_Controller();

		if ( ! empty( $posted_data['save_step'] ) && ! empty( $import_controller->steps[ $import_controller->step ]['handler'] ) ) {
			call_user_func( $import_controller->steps[ $import_controller->step ]['handler'], $import_controller );
		}

	}

	/**
	 * Disable the featured product column so that vendors can't import products and set them as featured.
	 *
	 * @param array $data the row data to filter.
	 * @return array $data the filtered row data.
	 */
	public function disable_featured_column( $data ) {

		if ( WCV_Vendors::is_vendor( get_current_user_id() ) ) {
			$data['featured'] = false;
		}

		return $data;
	}

	/**
	 * Check the product status based on the marketplace settings
	 *
	 * @param WC_Product $object The product object
	 * @param array      $data Raw CSV data.
	 */
	public function check_product_status( $object, $data ) {
		// Get publishing permission for marketplace and vendor override
		$can_submit_live  = wc_string_to_bool( get_option( 'wcvendors_capability_products_live', 'no' ) );
		$trusted_vendor   = wc_string_to_bool( get_user_meta( get_current_user_id(), '_wcv_trusted_vendor', true ) );
		$untrusted_vendor = wc_string_to_bool( get_user_meta( get_current_user_id(), '_wcv_untrusted_vendor', true ) );

		// If they can't submit live products, make the status pending
		if ( $can_submit_live ) {
			$object->set_status( 'publish' );
		} else {
			$object->set_status( 'pending' );
		}

		// If the vendor is untrusted make the status pending
		if ( $untrusted_vendor ) {
			$object->set_status( 'pending' );
		}

		// If the vendor is trusted they can publish straight away no matter the setting.
		if ( $trusted_vendor ) {
			$object->set_status( 'publish' );
		}

		return $object;
	}

	/**
	 * Check product owner to ensure that products can only be updated / inserted if owned by the vendor importing
	 *
	 * @param array $data import row to check.
	 */
	public function check_product_owner( $data ) {

		$vendor_id         = get_current_user_id();
		$import_product_id = $data['id'] ? $data['id'] : $data['sku'];

		// No product ID check the sku for product ID
		if ( empty( $data['id'] ) && ! empty( $data['sku'] ) ) {
			$import_product_id = wc_get_product_id_by_sku( $data['sku'] );
		}

		// Get the vendor id of the product
		$product_vendor_id = absint( get_post_field( 'post_author', $import_product_id ) );

		// remove product_id and sku to create a new product if the vendor doesn't own the product.
		if ( (int) $product_vendor_id !== (int) $vendor_id ) {
			$data['sku'] = 0;
			$data['id']  = 0;
		}

		return $data;
	}

	/**
	 * Ajax callback for importing one batch of products from a CSV.
	 */
	public function do_ajax_product_import() {
		global $wpdb;

		$post_data = wp_unslash( $_POST ); // phpcs:ignore

		if ( ! isset( $post_data['file'] ) ) {
			wp_die( -1 );
		}

		include_once $this->includes_dir . 'importers/class-wcv-product-csv-importer-controller.php';
		include_once $this->includes_dir . 'import/class-wcv-product-csv-importer.php';

		$file   = wc_clean( wp_unslash( $_POST['file'] ) ); // PHPCS: input var ok.
		$params = array(
			'delimiter'       => ! empty( $_POST['delimiter'] ) ? wc_clean( wp_unslash( $_POST['delimiter'] ) ) : ',', // PHPCS: input var ok.
			'start_pos'       => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0, // PHPCS: input var ok.
			'mapping'         => isset( $_POST['mapping'] ) ? (array) wc_clean( wp_unslash( $_POST['mapping'] ) ) : array(), // PHPCS: input var ok.
			'update_existing' => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false, // PHPCS: input var ok.
			'lines'           => apply_filters( 'woocommerce_product_import_batch_size', 30 ),
			'parse'           => true,
		);

		// Log failures.
		if ( 0 !== $params['start_pos'] ) {
			$error_log = array_filter( (array) get_user_option( 'product_import_error_log' ) );
		} else {
			$error_log = array();
		}

		$importer         = WCV_Product_CSV_Importer_Controller::get_importer( $file, $params );
		$results          = $importer->import();
		$percent_complete = $importer->get_percent_complete();
		$error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );

		update_user_option( get_current_user_id(), 'product_import_error_log', $error_log );

		if ( 100 === $percent_complete ) {
			// @codingStandardsIgnoreStart.
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_original_id' ) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'product',
				'post_status' => 'importing',
			) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'product_variation',
				'post_status' => 'importing',
			) );
			// @codingStandardsIgnoreEnd.

			// Clean up orphaned data.
			$wpdb->query(
				"
				DELETE {$wpdb->posts}.* FROM {$wpdb->posts}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->posts}.post_parent
				WHERE wp.ID IS NULL AND {$wpdb->posts}.post_type = 'product_variation'
			"
			);
			$wpdb->query(
				"
				DELETE {$wpdb->postmeta}.* FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->postmeta}.post_id
				WHERE wp.ID IS NULL
			"
			);
			// @codingStandardsIgnoreStart.
			$wpdb->query( "
				DELETE tr.* FROM {$wpdb->term_relationships} tr
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
				LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE wp.ID IS NULL
				AND tt.taxonomy IN ( '" . implode( "','", array_map( 'esc_sql', get_object_taxonomies( 'product' ) ) ) . "' )
			" );
			// @codingStandardsIgnoreEnd.

			$url_redirect = add_query_arg( array( '_wpnonce' => wp_create_nonce( 'woocommerce-csv-importer' ) ), WCVendors_Pro_Dashboard::get_dashboard_page_url() . 'product/import/?step=done' );
			if ( substr( $_SERVER['HTTP_REFERER'], 0, strlen( get_admin_url() ) ) === get_admin_url() ) {
				$url_redirect = add_query_arg( array( '_wpnonce' => wp_create_nonce( 'woocommerce-csv-importer' ) ), admin_url( 'edit.php?post_type=product&page=product_importer&step=done' ) );
			}

			// Send success.
			wp_send_json_success(
				array(
					'position'   => 'done',
					'percentage' => 100,
					'url'        => $url_redirect,
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'position'   => $importer->get_file_position(),
					'percentage' => $percent_complete,
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		}
	}

	// Export Handlers

	/**
	 * Serve the generated file.
	 */
	public function download_export_file() {
		if ( isset( $_GET['action'], $_GET['nonce'] ) && wp_verify_nonce( wp_unslash( $_GET['nonce'] ), 'product-csv' ) && 'download_product_csv' === wp_unslash( $_GET['action'] ) ) { // WPCS: input var ok, sanitization ok.
			include_once 'includes/import-export/export/class-wcv-product-csv-exporter.php';
			$exporter = new WCV_Product_CSV_Exporter();

			if ( ! empty( $_GET['filename'] ) ) { // WPCS: input var ok.
				$exporter->set_filename( wp_unslash( $_GET['filename'] ) ); // WPCS: input var ok, sanitization ok.
			}

			$exporter->export();
		}
	}

	/**
	 * AJAX callback for doing the actual export to the CSV file.
	 */
	public function do_ajax_product_export() {
		check_ajax_referer( 'wcv-product-export', 'security' );

		// if ( get_option( 'wcvendors_capability_products_export', false ) ) {
		// wp_send_json_error( array( 'message' => __( 'Insufficient privileges to export products.', 'wcvendors-pro' ) ) );
		// }

		include_once 'includes/import-export/export/class-wcv-product-csv-exporter.php';

		$step     = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 1; // WPCS: input var ok, sanitization ok.
		$exporter = new WCV_Product_CSV_Exporter();

		if ( ! empty( $_POST['columns'] ) ) { // WPCS: input var ok.
			$exporter->set_column_names( wp_unslash( $_POST['columns'] ) ); // WPCS: input var ok, sanitization ok.
		}

		if ( ! empty( $_POST['selected_columns'] ) ) { // WPCS: input var ok.
			$exporter->set_columns_to_export( wp_unslash( $_POST['selected_columns'] ) ); // WPCS: input var ok, sanitization ok.
		}

		if ( ! empty( $_POST['export_meta'] ) ) { // WPCS: input var ok.
			$exporter->enable_meta_export( true );
		}

		if ( ! empty( $_POST['export_types'] ) ) { // WPCS: input var ok.
			$exporter->set_product_types_to_export( wp_unslash( $_POST['export_types'] ) ); // WPCS: input var ok, sanitization ok.
		}

		if ( ! empty( $_POST['export_category'] ) && is_array( $_POST['export_category'] ) ) {// WPCS: input var ok.
			$exporter->set_product_category_to_export( wp_unslash( array_values( $_POST['export_category'] ) ) ); // WPCS: input var ok, sanitization ok.
		}

		if ( ! empty( $_POST['filename'] ) ) { // WPCS: input var ok.
			$exporter->set_filename( wp_unslash( $_POST['filename'] ) ); // WPCS: input var ok, sanitization ok.
		}

		$exporter->set_page( $step );
		$exporter->generate_file();

		$query_args = apply_filters(
			'wcvendors_export_get_ajax_query_args',
			array(
				'nonce'    => wp_create_nonce( 'product-csv' ),
				'action'   => 'download_product_csv',
				'filename' => $exporter->get_filename(),
			)
		);

		if ( 100 === $exporter->get_percent_complete() ) {
			wp_send_json_success(
				array(
					'step'       => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( $query_args, WCVendors_Pro_Dashboard::get_dashboard_page_url( '/product/export/' ) ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'step'       => ++$step,
					'percentage' => $exporter->get_percent_complete(),
					'columns'    => $exporter->get_column_names(),
				)
			);
		}
	}

}
