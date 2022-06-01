<?php
/**
 * Class WCV_Product_CSV_Importer_Controller file.
 *
 * @package WooCommerce\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Importer' ) ) {
	return;
}

/**
 * Product importer controller - handles file upload and forms in admin.
 *
 * @package     WCVendors\Public\Dashboard\Importers
 * @version     3.1.0
 */
class WCV_Product_CSV_Importer_Controller {

	/**
	 * The path to the current file.
	 *
	 * @var string
	 */
	protected $file = '';

	/**
	 * The current import step.
	 *
	 * @var string
	 */
	public $step = '';

	/**
	 * Progress steps.
	 *
	 * @var array
	 */
	public $steps = array();

	/**
	 * Errors.
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * The current delimiter for the file being read.
	 *
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * Whether to use previous mapping selections.
	 *
	 * @var bool
	 */
	protected $map_preferences = false;

	/**
	 * Whether to skip existing products.
	 *
	 * @var bool
	 */
	protected $update_existing = false;

	/**
	 * Get importer instance.
	 *
	 * @param  string $file File to import.
	 * @param  array  $args Importer arguments.
	 * @return WCV_Product_CSV_Importer
	 */
	public static function get_importer( $file, $args = array() ) {
		$importer_class = apply_filters( 'woocommerce_product_csv_importer_class', 'WCV_Product_CSV_Importer' );
		$args           = apply_filters( 'woocommerce_product_csv_importer_args', $args, $importer_class );
		return new $importer_class( $file, $args );
	}

	/**
	 * Check whether a file is a valid CSV file.
	 *
	 * @todo Replace this method with wc_is_file_valid_csv() function.
	 * @param string $file File path.
	 * @param bool   $check_path Whether to also check the file is located in a valid location (Default: true).
	 * @return bool
	 */
	public static function is_file_valid_csv( $file, $check_path = true ) {
		if ( $check_path && apply_filters( 'woocommerce_product_csv_importer_check_import_file_path', true ) && false !== stripos( $file, '://' ) ) {
			return false;
		}

		$valid_filetypes = self::get_valid_csv_filetypes();
		$filetype        = wp_check_filetype( $file, $valid_filetypes );
		if ( in_array( $filetype['type'], $valid_filetypes, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get all the valid filetypes for a CSV file.
	 *
	 * @return array
	 */
	protected static function get_valid_csv_filetypes() {
		return apply_filters(
			'woocommerce_csv_product_import_valid_filetypes',
			array(
				'csv' => 'text/csv',
				'txt' => 'text/plain',
			)
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$default_steps = array(
			'upload'  => array(
				'name'    => __( 'Upload CSV file', 'wcvendors-pro' ),
				'view'    => array( $this, 'upload_form' ),
				'handler' => array( $this, 'upload_form_handler' ),
			),
			'mapping' => array(
				'name'    => __( 'Column mapping', 'wcvendors-pro' ),
				'view'    => array( $this, 'mapping_form' ),
				'handler' => '',
			),
			'import'  => array(
				'name'    => __( 'Import', 'wcvendors-pro' ),
				'view'    => array( $this, 'import' ),
				'handler' => '',
			),
			'done'    => array(
				'name'    => __( 'Done!', 'wcvendors-pro' ),
				'view'    => array( $this, 'done' ),
				'handler' => '',
			),
		);

		$this->steps = apply_filters( 'woocommerce_product_csv_importer_steps', $default_steps );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$this->step            = isset( $_REQUEST['step'] ) ? sanitize_key( $_REQUEST['step'] ) : current( array_keys( $this->steps ) );
		$this->file            = isset( $_REQUEST['file'] ) ? wc_clean( wp_unslash( $_REQUEST['file'] ) ) : '';
		$this->update_existing = isset( $_REQUEST['update_existing'] ) ? (bool) $_REQUEST['update_existing'] : false;
		$this->delimiter       = ! empty( $_REQUEST['delimiter'] ) ? wc_clean( wp_unslash( $_REQUEST['delimiter'] ) ) : ',';
		$this->map_preferences = isset( $_REQUEST['map_preferences'] ) ? (bool) $_REQUEST['map_preferences'] : false;
		// phpcs:enable

		// Import mappings for CSV data.
		include_once dirname( __FILE__ ) . '/mappings/mappings.php';

		if ( $this->map_preferences ) {
			add_filter( 'woocommerce_csv_product_import_mapped_columns', array( $this, 'auto_map_user_preferences' ), 9999 );
		}
	}

	/**
	 * Get the URL for the next step's screen.
	 *
	 * @param string $step  slug (default: current step).
	 * @return string       URL for next step if a next step exists.
	 *                      Admin URL if it's the last step.
	 *                      Empty string on failure.
	 */
	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );

		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );

		if ( false === $step_index ) {
			return '';
		}

		$params = array(
			'step'            => $keys[ $step_index + 1 ],
			'file'            => str_replace( DIRECTORY_SEPARATOR, '/', $this->file ),
			'delimiter'       => $this->delimiter,
			'update_existing' => $this->update_existing,
			'map_preferences' => $this->map_preferences,
			'_wpnonce'        => wp_create_nonce( 'woocommerce-csv-importer' ), // wp_nonce_url() escapes & to &amp; breaking redirects.
		);

		return add_query_arg( $params );
	}

	/**
	 * Output header view.
	 */
	protected function output_header() {
		include dirname( __FILE__ ) . '/views/html-csv-import-header.php';
	}

	/**
	 * Output steps view.
	 */
	protected function output_steps() {
		include dirname( __FILE__ ) . '/views/html-csv-import-steps.php';
	}

	/**
	 * Output footer view.
	 */
	protected function output_footer() {
		include dirname( __FILE__ ) . '/views/html-csv-import-footer.php';
	}

	/**
	 * Add error message.
	 *
	 * @param string $message Error message.
	 * @param array  $actions List of actions with 'url' and 'label'.
	 */
	protected function add_error( $message, $actions = array() ) {
		$this->errors[] = array(
			'message' => $message,
			'actions' => $actions,
		);
	}

	/**
	 * Add error message.
	 */
	protected function output_errors() {
		if ( ! $this->errors ) {
			return;
		}

		foreach ( $this->errors as $error ) {
			echo '<div class="error inline">';
			echo '<p>' . esc_html( $error['message'] ) . '</p>';

			if ( ! empty( $error['actions'] ) ) {
				echo '<p>';
				foreach ( $error['actions'] as $action ) {
					echo '<a class="button button-primary" href="' . esc_url( $action['url'] ) . '">' . esc_html( $action['label'] ) . '</a> ';
				}
				echo '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Dispatch current step and show correct view.
	 */
	public function dispatch() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST['save_step'] ) && ! empty( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}
		$this->output_header();
		$this->output_steps();
		$this->output_errors();
		call_user_func( $this->steps[ $this->step ]['view'], $this );
		$this->output_footer();
	}

	/**
	 * Output information about the uploading process.
	 */
	protected function upload_form() {
		$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size       = size_format( $bytes );
		$upload_dir = wp_upload_dir();

		include dirname( __FILE__ ) . '/views/html-product-csv-import-form.php';
	}

	/**
	 * Handle the upload form and store options.
	 */
	public function upload_form_handler() {
		check_admin_referer( 'woocommerce-csv-importer' );

		$file = $this->handle_upload();

		if ( is_wp_error( $file ) ) {
			$this->add_error( $file->get_error_message() );
			return;
		} else {
			$this->file = $file;
		}

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Handles the CSV upload and initial parsing of the file to prepare for
	 * displaying author import options.
	 *
	 * @return string|WP_Error
	 */
	public function handle_upload() {

		  // Include file upload handler
		require_once ABSPATH . 'wp-admin/includes/admin.php';
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce already verified in WCV_Product_CSV_Importer_Controller::upload_form_handler()
		$file_url = isset( $_POST['file_url'] ) ? wc_clean( wp_unslash( $_POST['file_url'] ) ) : '';

		if ( empty( $file_url ) ) {
			if ( ! isset( $_FILES['import'] ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_file_empty', __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'wcvendors-pro' ) );
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
			if ( ! self::is_file_valid_csv( wc_clean( wp_unslash( $_FILES['import']['name'] ) ), false ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_file_invalid', __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'wcvendors-pro' ) );
			}

			$overrides = array(
				'test_form' => false,
				'mimes'     => self::get_valid_csv_filetypes(),
			);
			$import    = $_FILES['import']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$upload    = wp_handle_upload( $import, $overrides );

			if ( isset( $upload['error'] ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_error', $upload['error'] );
			}

			// Construct the object array.
			$object = array(
				'post_title'     => basename( $upload['file'] ),
				'post_content'   => $upload['url'],
				'post_mime_type' => $upload['type'],
				'guid'           => $upload['url'],
				'context'        => 'import',
				'post_status'    => 'private',
			);

			// Save the data.
			$id = wp_insert_attachment( $object, $upload['file'] );

			/*
			 * Schedule a cleanup for one day from now in case of failed
			 * import or missing wp_import_cleanup() call.
			 */
			wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

			return $upload['file'];
		} elseif ( file_exists( ABSPATH . $file_url ) ) {
			if ( ! self::is_file_valid_csv( ABSPATH . $file_url ) ) {
				return new WP_Error( 'woocommerce_product_csv_importer_upload_file_invalid', __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'wcvendors-pro' ) );
			}

			return ABSPATH . $file_url;
		}
		// phpcs:enable

		return new WP_Error( 'woocommerce_product_csv_importer_upload_invalid_file', __( 'Please upload or provide the link to a valid CSV file.', 'wcvendors-pro' ) );
	}

	/**
	 * Mapping step.
	 */
	protected function mapping_form() {
		check_admin_referer( 'woocommerce-csv-importer' );
		$args = array(
			'lines'     => 1,
			'delimiter' => $this->delimiter,
		);

		$importer     = self::get_importer( $this->file, $args );
		$headers      = $importer->get_raw_keys();
		$mapped_items = $this->auto_map_columns( $headers );
		$sample       = current( $importer->get_raw_data() );

		if ( empty( $sample ) ) {
			$this->add_error(
				__( 'The file is empty or using a different encoding than UTF-8, please try again with a new file.', 'wcvendors-pro' ),
				array(
					array(
						'url'   => admin_url( 'edit.php?post_type=product&page=product_importer' ),
						'label' => __( 'Upload a new file', 'wcvendors-pro' ),
					),
				)
			);

			// Force output the errors in the same page.
			$this->output_errors();
			return;
		}

		include_once dirname( __FILE__ ) . '/views/html-csv-import-mapping.php';
	}

	/**
	 * Import the file if it exists and is valid.
	 */
	public function import() {
		// Displaying this page triggers Ajax action to run the import with a valid nonce,
		// therefore this page needs to be nonce protected as well.
		check_admin_referer( 'woocommerce-csv-importer' );

		if ( ! self::is_file_valid_csv( $this->file ) ) {
			$this->add_error( __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'wcvendors-pro' ) );
			$this->output_errors();
			return;
		}

		if ( ! is_file( $this->file ) ) {
			$this->add_error( __( 'The file does not exist, please try again.', 'wcvendors-pro' ) );
			$this->output_errors();
			return;
		}

		if ( ! empty( $_POST['map_from'] ) && ! empty( $_POST['map_to'] ) ) {
			$mapping_from = wc_clean( wp_unslash( $_POST['map_from'] ) );
			$mapping_to   = wc_clean( wp_unslash( $_POST['map_to'] ) );

			// Save mapping preferences for future imports.
			update_user_option( get_current_user_id(), 'woocommerce_product_import_mapping', $mapping_to );
		} else {
			wp_redirect( esc_url_raw( $this->get_next_step_link( 'upload' ) ) );
			exit;
		}

		wp_localize_script(
			'wc-product-import',
			'wc_product_import_params',
			array(
				'import_nonce'    => wp_create_nonce( 'wc-product-import' ),
				'mapping'         => array(
					'from' => $mapping_from,
					'to'   => $mapping_to,
				),
				'file'            => $this->file,
				'update_existing' => $this->update_existing,
				'delimiter'       => $this->delimiter,
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
			)
		);
		wp_enqueue_script( 'wc-product-import' );

		include_once dirname( __FILE__ ) . '/views/html-csv-import-progress.php';
	}

	/**
	 * Done step.
	 */
	protected function done() {
		check_admin_referer( 'woocommerce-csv-importer' );
		$imported  = isset( $_GET['products-imported'] ) ? absint( $_GET['products-imported'] ) : 0;
		$updated   = isset( $_GET['products-updated'] ) ? absint( $_GET['products-updated'] ) : 0;
		$failed    = isset( $_GET['products-failed'] ) ? absint( $_GET['products-failed'] ) : 0;
		$skipped   = isset( $_GET['products-skipped'] ) ? absint( $_GET['products-skipped'] ) : 0;
		$file_name = isset( $_GET['file-name'] ) ? sanitize_text_field( wp_unslash( $_GET['file-name'] ) ) : '';
		$errors    = array_filter( (array) get_user_option( 'product_import_error_log' ) );

		include_once dirname( __FILE__ ) . '/views/html-csv-import-done.php';
	}

	/**
	 * Columns to normalize.
	 *
	 * @param  array $columns List of columns names and keys.
	 * @return array
	 */
	protected function normalize_columns_names( $columns ) {
		$normalized = array();

		foreach ( $columns as $key => $value ) {
			$normalized[ strtolower( $key ) ] = $value;
		}

		return $normalized;
	}

	/**
	 * Auto map column names.
	 *
	 * @param  array $raw_headers Raw header columns.
	 * @param  bool  $num_indexes If should use numbers or raw header columns as indexes.
	 * @return array
	 */
	protected function auto_map_columns( $raw_headers, $num_indexes = true ) {
		$weight_unit    = get_option( 'woocommerce_weight_unit' );
		$dimension_unit = get_option( 'woocommerce_dimension_unit' );

		/*
		 * @hooked wc_importer_generic_mappings - 10
		 * @hooked wc_importer_wordpress_mappings - 10
		 * @hooked wc_importer_default_english_mappings - 100
		 */
		$default_columns = $this->normalize_columns_names(
			apply_filters(
				'woocommerce_csv_product_import_mapping_default_columns',
				array(
					__( 'ID', 'wcvendors-pro' )            => 'id',
					__( 'Type', 'wcvendors-pro' )          => 'type',
					__( 'SKU', 'wcvendors-pro' )           => 'sku',
					__( 'Name', 'wcvendors-pro' )          => 'name',
					__( 'Published', 'wcvendors-pro' )     => 'published',
					__( 'Is featured?', 'wcvendors-pro' )  => 'featured',
					__( 'Visibility in catalog', 'wcvendors-pro' ) => 'catalog_visibility',
					__( 'Short description', 'wcvendors-pro' ) => 'short_description',
					__( 'Description', 'wcvendors-pro' )   => 'description',
					__( 'Date sale price starts', 'wcvendors-pro' ) => 'date_on_sale_from',
					__( 'Date sale price ends', 'wcvendors-pro' ) => 'date_on_sale_to',
					__( 'Tax status', 'wcvendors-pro' )    => 'tax_status',
					__( 'Tax class', 'wcvendors-pro' )     => 'tax_class',
					__( 'In stock?', 'wcvendors-pro' )     => 'stock_status',
					__( 'Stock', 'wcvendors-pro' )         => 'stock_quantity',
					__( 'Backorders allowed?', 'wcvendors-pro' ) => 'backorders',
					__( 'Low stock amount', 'wcvendors-pro' ) => 'low_stock_amount',
					__( 'Sold individually?', 'wcvendors-pro' ) => 'sold_individually',
					/* translators: %s: Weight unit */
					sprintf( __( 'Weight (%s)', 'wcvendors-pro' ), $weight_unit ) => 'weight',
					/* translators: %s: Length unit */
					sprintf( __( 'Length (%s)', 'wcvendors-pro' ), $dimension_unit ) => 'length',
					/* translators: %s: Width unit */
					sprintf( __( 'Width (%s)', 'wcvendors-pro' ), $dimension_unit ) => 'width',
					/* translators: %s: Height unit */
					sprintf( __( 'Height (%s)', 'wcvendors-pro' ), $dimension_unit ) => 'height',
					__( 'Allow customer reviews?', 'wcvendors-pro' ) => 'reviews_allowed',
					__( 'Purchase note', 'wcvendors-pro' ) => 'purchase_note',
					__( 'Sale price', 'wcvendors-pro' )    => 'sale_price',
					__( 'Regular price', 'wcvendors-pro' ) => 'regular_price',
					__( 'Categories', 'wcvendors-pro' )    => 'category_ids',
					__( 'Tags', 'wcvendors-pro' )          => 'tag_ids',
					__( 'Shipping class', 'wcvendors-pro' ) => 'shipping_class_id',
					__( 'Images', 'wcvendors-pro' )        => 'images',
					__( 'Download limit', 'wcvendors-pro' ) => 'download_limit',
					__( 'Download expiry days', 'wcvendors-pro' ) => 'download_expiry',
					__( 'Parent', 'wcvendors-pro' )        => 'parent_id',
					__( 'Upsells', 'wcvendors-pro' )       => 'upsell_ids',
					__( 'Cross-sells', 'wcvendors-pro' )   => 'cross_sell_ids',
					__( 'Grouped products', 'wcvendors-pro' ) => 'grouped_products',
					__( 'External URL', 'wcvendors-pro' )  => 'product_url',
					__( 'Button text', 'wcvendors-pro' )   => 'button_text',
					__( 'Position', 'wcvendors-pro' )      => 'menu_order',
				),
				$raw_headers
			)
		);

		$special_columns = $this->get_special_columns(
			$this->normalize_columns_names(
				apply_filters(
					'woocommerce_csv_product_import_mapping_special_columns',
					array(
						/* translators: %d: Attribute number */
						__( 'Attribute %d name', 'wcvendors-pro' ) => 'attributes:name',
						/* translators: %d: Attribute number */
						__( 'Attribute %d value(s)', 'wcvendors-pro' ) => 'attributes:value',
						/* translators: %d: Attribute number */
						__( 'Attribute %d visible', 'wcvendors-pro' ) => 'attributes:visible',
						/* translators: %d: Attribute number */
						__( 'Attribute %d global', 'wcvendors-pro' ) => 'attributes:taxonomy',
						/* translators: %d: Attribute number */
						__( 'Attribute %d default', 'wcvendors-pro' ) => 'attributes:default',
						/* translators: %d: Download number */
						__( 'Download %d name', 'wcvendors-pro' ) => 'downloads:name',
						/* translators: %d: Download number */
						__( 'Download %d URL', 'wcvendors-pro' ) => 'downloads:url',
						/* translators: %d: Meta number */
						__( 'Meta: %s', 'wcvendors-pro' ) => 'meta:',
					),
					$raw_headers
				)
			)
		);

		$headers = array();
		foreach ( $raw_headers as $key => $field ) {
			$normalized_field  = strtolower( $field );
			$index             = $num_indexes ? $key : $field;
			$headers[ $index ] = $normalized_field;

			if ( isset( $default_columns[ $normalized_field ] ) ) {
				$headers[ $index ] = $default_columns[ $normalized_field ];
			} else {
				foreach ( $special_columns as $regex => $special_key ) {
					// Don't use the normalized field in the regex since meta might be case-sensitive.
					if ( preg_match( $regex, $field, $matches ) ) {
						$headers[ $index ] = $special_key . $matches[1];
						break;
					}
				}
			}
		}

		return apply_filters( 'woocommerce_csv_product_import_mapped_columns', $headers, $raw_headers );
	}

	/**
	 * Map columns using the user's lastest import mappings.
	 *
	 * @param  array $headers Header columns.
	 * @return array
	 */
	public function auto_map_user_preferences( $headers ) {
		$mapping_preferences = get_user_option( 'woocommerce_product_import_mapping' );

		if ( ! empty( $mapping_preferences ) && is_array( $mapping_preferences ) ) {
			return $mapping_preferences;
		}

		return $headers;
	}

	/**
	 * Sanitize special column name regex.
	 *
	 * @param  string $value Raw special column name.
	 * @return string
	 */
	protected function sanitize_special_column_name_regex( $value ) {
		return '/' . str_replace( array( '%d', '%s' ), '(.*)', trim( quotemeta( $value ) ) ) . '/i';
	}

	/**
	 * Get special columns.
	 *
	 * @param  array $columns Raw special columns.
	 * @return array
	 */
	protected function get_special_columns( $columns ) {
		$formatted = array();

		foreach ( $columns as $key => $value ) {
			$regex = $this->sanitize_special_column_name_regex( $key );

			$formatted[ $regex ] = $value;
		}

		return $formatted;
	}

	/**
	 * Get mapping options.
	 *
	 * @param  string $item Item name.
	 * @return array
	 */
	protected function get_mapping_options( $item = '' ) {
		// Get index for special column names.
		$index = $item;

		if ( preg_match( '/\d+/', $item, $matches ) ) {
			$index = $matches[0];
		}

		// Properly format for meta field.
		$meta = str_replace( 'meta:', '', $item );

		// Available options.
		$weight_unit    = get_option( 'woocommerce_weight_unit' );
		$dimension_unit = get_option( 'woocommerce_dimension_unit' );
		$options        = array(
			'id'                 => __( 'ID', 'wcvendors-pro' ),
			'type'               => __( 'Type', 'wcvendors-pro' ),
			'sku'                => __( 'SKU', 'wcvendors-pro' ),
			'name'               => __( 'Name', 'wcvendors-pro' ),
			'published'          => __( 'Published', 'wcvendors-pro' ),
			'featured'           => __( 'Is featured?', 'wcvendors-pro' ),
			'catalog_visibility' => __( 'Visibility in catalog', 'wcvendors-pro' ),
			'short_description'  => __( 'Short description', 'wcvendors-pro' ),
			'description'        => __( 'Description', 'wcvendors-pro' ),
			'price'              => array(
				'name'    => __( 'Price', 'wcvendors-pro' ),
				'options' => array(
					'regular_price'     => __( 'Regular price', 'wcvendors-pro' ),
					'sale_price'        => __( 'Sale price', 'wcvendors-pro' ),
					'date_on_sale_from' => __( 'Date sale price starts', 'wcvendors-pro' ),
					'date_on_sale_to'   => __( 'Date sale price ends', 'wcvendors-pro' ),
				),
			),
			'tax_status'         => __( 'Tax status', 'wcvendors-pro' ),
			'tax_class'          => __( 'Tax class', 'wcvendors-pro' ),
			'stock_status'       => __( 'In stock?', 'wcvendors-pro' ),
			'stock_quantity'     => _x( 'Stock', 'Quantity in stock', 'wcvendors-pro' ),
			'backorders'         => __( 'Backorders allowed?', 'wcvendors-pro' ),
			'low_stock_amount'   => __( 'Low stock amount', 'wcvendors-pro' ),
			'sold_individually'  => __( 'Sold individually?', 'wcvendors-pro' ),
			/* translators: %s: weight unit */
			'weight'             => sprintf( __( 'Weight (%s)', 'wcvendors-pro' ), $weight_unit ),
			'dimensions'         => array(
				'name'    => __( 'Dimensions', 'wcvendors-pro' ),
				'options' => array(
					/* translators: %s: dimension unit */
					'length' => sprintf( __( 'Length (%s)', 'wcvendors-pro' ), $dimension_unit ),
					/* translators: %s: dimension unit */
					'width'  => sprintf( __( 'Width (%s)', 'wcvendors-pro' ), $dimension_unit ),
					/* translators: %s: dimension unit */
					'height' => sprintf( __( 'Height (%s)', 'wcvendors-pro' ), $dimension_unit ),
				),
			),
			'category_ids'       => __( 'Categories', 'wcvendors-pro' ),
			'tag_ids'            => __( 'Tags (comma separated)', 'wcvendors-pro' ),
			'tag_ids_spaces'     => __( 'Tags (space separated)', 'wcvendors-pro' ),
			'shipping_class_id'  => __( 'Shipping class', 'wcvendors-pro' ),
			'images'             => __( 'Images', 'wcvendors-pro' ),
			'parent_id'          => __( 'Parent', 'wcvendors-pro' ),
			'upsell_ids'         => __( 'Upsells', 'wcvendors-pro' ),
			'cross_sell_ids'     => __( 'Cross-sells', 'wcvendors-pro' ),
			'grouped_products'   => __( 'Grouped products', 'wcvendors-pro' ),
			'external'           => array(
				'name'    => __( 'External product', 'wcvendors-pro' ),
				'options' => array(
					'product_url' => __( 'External URL', 'wcvendors-pro' ),
					'button_text' => __( 'Button text', 'wcvendors-pro' ),
				),
			),
			'downloads'          => array(
				'name'    => __( 'Downloads', 'wcvendors-pro' ),
				'options' => array(
					'downloads:name' . $index => __( 'Download name', 'wcvendors-pro' ),
					'downloads:url' . $index  => __( 'Download URL', 'wcvendors-pro' ),
					'download_limit'          => __( 'Download limit', 'wcvendors-pro' ),
					'download_expiry'         => __( 'Download expiry days', 'wcvendors-pro' ),
				),
			),
			'attributes'         => array(
				'name'    => __( 'Attributes', 'wcvendors-pro' ),
				'options' => array(
					'attributes:name' . $index     => __( 'Attribute name', 'wcvendors-pro' ),
					'attributes:value' . $index    => __( 'Attribute value(s)', 'wcvendors-pro' ),
					'attributes:taxonomy' . $index => __( 'Is a global attribute?', 'wcvendors-pro' ),
					'attributes:visible' . $index  => __( 'Attribute visibility', 'wcvendors-pro' ),
					'attributes:default' . $index  => __( 'Default attribute', 'wcvendors-pro' ),
				),
			),
			'reviews_allowed'    => __( 'Allow customer reviews?', 'wcvendors-pro' ),
			'purchase_note'      => __( 'Purchase note', 'wcvendors-pro' ),
			'meta:' . $meta      => __( 'Import as meta data', 'wcvendors-pro' ),
			'menu_order'         => __( 'Position', 'wcvendors-pro' ),
		);

		return apply_filters( 'woocommerce_csv_product_import_mapping_options', $options, $item );
	}
}
