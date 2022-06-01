<?php
/**
 * Generic mappings
 *
 * @package WooCommerce\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add generic mappings.
 *
 * @since 3.1.0
 * @param array $mappings Importer columns mappings.
 * @return array
 */
function wc_importer_generic_mappings( $mappings ) {
	$generic_mappings = array(
		__( 'Title', 'wcvendors-pro' )         => 'name',
		__( 'Product Title', 'wcvendors-pro' ) => 'name',
		__( 'Price', 'wcvendors-pro' )         => 'regular_price',
		__( 'Parent SKU', 'wcvendors-pro' )    => 'parent_id',
		__( 'Quantity', 'wcvendors-pro' )      => 'stock_quantity',
		__( 'Menu order', 'wcvendors-pro' )    => 'menu_order',
	);

	return array_merge( $mappings, $generic_mappings );
}
add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'wc_importer_generic_mappings' );
