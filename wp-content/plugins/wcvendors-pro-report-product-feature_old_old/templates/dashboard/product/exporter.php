<?php
/**
 * Dashboard: Product Export
 *
 * @package WCVendors_Pro\Dashboard\Product\Export
 * @version    1.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_script( 'wcv-product-export' );

?>
<h1><?php esc_html_e( 'Export Products', 'wcvendors-pro' ); ?></h1>

<div class="wcv-exporter-wrapper">
	<form class="wcv-exporter">
		<header>
			<span class="spinner is-active"></span>
			<p><?php esc_html_e( 'This tool allows you to generate and download a CSV file containing a list of all your products.', 'wcvendors-pro' ); ?></p>
		</header>
		<section>
			<table class="form-table wcv-exporter-options">
				<tbody>
					<tr>
						<th scope="row">
							<label for="wcv-exporter-columns"><?php esc_html_e( 'Which columns should be exported?', 'wcvendors-pro' ); ?></label>
						</th>
						<td>
							<select id="wcv-exporter-columns" class="wcv-exporter-columns select2" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all columns', 'wcvendors-pro' ); ?>">
								<?php
								foreach ( $exporter->get_default_column_names() as $column_id => $column_name ) {
									echo '<option value="' . esc_attr( $column_id ) . '">' . esc_html( $column_name ) . '</option>';
								}
								?>
								<option value="downloads"><?php esc_html_e( 'Downloads', 'wcvendors-pro' ); ?></option>
								<option value="attributes"><?php esc_html_e( 'Attributes', 'wcvendors-pro' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wcv-exporter-types"><?php esc_html_e( 'Which product types should be exported?', 'wcvendors-pro' ); ?></label>
						</th>
						<td>
							<select id="wcv-exporter-types" class="wcv-exporter-types select2" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all products', 'wcvendors-pro' ); ?>">
								<?php
								foreach ( WC_Admin_Exporters::get_product_types() as $value => $label ) {
									echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wcv-exporter-category"><?php esc_html_e( 'Which product category should be exported?', 'wcvendors-pro' ); ?></label>
						</th>
						<td>
							<select id="wcv-exporter-category" class="wcv-exporter-category select2" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all categories', 'wcvendors-pro' ); ?>">
							<?php
							$vendor_categories = WCVendors_Pro_Vendor_Controller::get_categories( get_current_user_id() );
							$categories        = get_categories(
								array(
									'taxonomy'   => 'product_cat',
									'hide_empty' => false,
									'include'    => array_keys( $vendor_categories ),
								)
							);
							foreach ( $categories as $category ) {
								echo '<option value="' . esc_attr( $category->slug ) . '">' . esc_html( $category->name ) . '</option>';
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wcv-exporter-meta"><?php esc_html_e( 'Export custom meta?', 'wcvendors-pro' ); ?></label>
						</th>
						<td>
							<input type="checkbox" id="wcv-exporter-meta" value="1" />
							<label for="wcv-exporter-meta"><?php esc_html_e( 'Yes, export all custom meta', 'wcvendors-pro' ); ?></label>
						</td>
					</tr>
					<?php do_action( 'wcvendors_product_export_row' ); ?>
				</tbody>
			</table>
			<progress class="wcv-exporter-progress" max="100" value="0"></progress>
		</section>
		<div class="wc-actions">
			<button type="submit" class="wcv-exporter-button button" value="<?php esc_attr_e( 'Generate CSV', 'wcvendors-pro' ); ?>"><?php esc_html_e( 'Generate CSV', 'wcvendors-pro' ); ?></button>
		</div>
	</form>
</div>
