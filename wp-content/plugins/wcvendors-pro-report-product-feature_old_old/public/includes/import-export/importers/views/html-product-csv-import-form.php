<?php
/**
 * Admin View: Product import form
 *
 * @package WCVendors\Public\Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="wc-progress-form-content wcv-importer" enctype="multipart/form-data" method="post">
	<header>
		<h2><?php esc_html_e( 'Import products from a CSV file', 'wcv' ); ?></h2>
		<p><?php esc_html_e( 'This tool allows you to import (or merge) product data to your store from a CSV or TXT file.', 'wcv' ); ?></p>
	</header>
	<section>
		<table class="form-table wcv-importer-options">
			<tbody>
				<tr>
					<th scope="row">
						<label for="upload">
							<?php esc_html_e( 'Choose a CSV file from your computer:', 'wcv' ); ?>
						</label>
					</th>
					<td>
						<?php
						if ( ! empty( $upload_dir['error'] ) ) {
							?>
							<div class="inline error">
								<p><?php esc_html_e( 'Before you can upload your import file, you will need to fix the following error:', 'wcv' ); ?></p>
								<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
							</div>
							<?php
						} else {
							?>
							<input type="file" id="upload" name="import" size="25" />
							<input type="hidden" name="action" value="save" />
							<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
							<br>
							<small>
								<?php
								printf(
									/* translators: %s: maximum upload size */
									esc_html__( 'Maximum size: %s', 'wcv' ),
									esc_html( $size )
								);
								?>
							</small>
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<th><label for="wcv-importer-update-existing"><?php esc_html_e( 'Update existing products', 'wcv' ); ?></label><br/></th>
					<td>
						<input type="hidden" name="update_existing" value="0" />
						<input type="checkbox" id="wcv-importer-update-existing" name="update_existing" value="1" />
						<label for="wcv-importer-update-existing"><?php esc_html_e( 'Existing products that match by ID or SKU will be updated. Products that do not exist will be skipped.', 'wcv' ); ?></label>
					</td>
				</tr>
				<tr class="wcv-importer-advanced hidden">
					<th><label><?php esc_html_e( 'CSV Delimiter', 'wcv' ); ?></label><br/></th>
					<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
				</tr>
				<tr class="wcv-importer-advanced hidden">
					<th><label><?php esc_html_e( 'Use previous column mapping preferences?', 'wcv' ); ?></label><br/></th>
					<td><input type="checkbox" id="wcv-importer-map-preferences" name="map_preferences" value="1" /></td>
				</tr>
			</tbody>
		</table>
	</section>
	<script type="text/javascript">
		jQuery(function() {
			jQuery( '.wcv-importer-toggle-advanced-options' ).on( 'click', function() {
				var elements = jQuery( '.wcv-importer-advanced' );
				if ( elements.is( '.hidden' ) ) {
					elements.removeClass( 'hidden' );
					jQuery( this ).text( jQuery( this ).data( 'hidetext' ) );
				} else {
					elements.addClass( 'hidden' );
					jQuery( this ).text( jQuery( this ).data( 'showtext' ) );
				}
				return false;
			} );
		});
	</script>
	<div class="wcv-actions  wcv-cols-group">
		<div class="all-50 align-left">
			<a href="#" class="wcv-importer-toggle-advanced-options" data-hidetext="<?php esc_attr_e( 'Hide advanced options', 'wcvendors-pro' ); ?>" data-showtext="<?php esc_attr_e( 'Show advanced options', 'wcvendors-pro' ); ?>"><?php esc_html_e( 'Show advanced options', 'wcvendors-pro' ); ?></a>
		</div>
		<div class="all-50 align-right">
			<button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Continue', 'wcvendors-pro' ); ?>" name="save_step"><?php esc_html_e( 'Continue', 'wcvendors-pro' ); ?></button>
			<?php wp_nonce_field( 'woocommerce-csv-importer' ); ?>
		</div>
	</div>
</form>
