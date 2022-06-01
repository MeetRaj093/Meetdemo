<?php
/**
 * Respond popup form.
 *
 * @package WCVendors_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>


<div class="wcv-form-group">
	<label class="form-label"><?php esc_html_e( 'Reporter', 'wcvendors-pro' ); ?></label>
	<span> <?php echo esc_html( $reporter->display_name ); ?></span>
</div>
<div class="wcv-form-group">
	<label class="form-label"><?php esc_html_e( 'Vendor', 'wcvendors-pro' ); ?></label>
	<span> <?php echo esc_html( WCV_Vendors::get_vendor_shop_name( $report_data->vendor_id ) ); ?></span>
</div>
<div class="wcv-form-group">
	<label class="form-label"><?php esc_html_e( 'Respond to' ); ?></label>
	<input class="control" type="radio" name="respond_to" value="vendor" checked>
	<span><?php esc_html_e( 'Vendor', 'wcvendors-pro' ); ?></span>
	<input type="radio" name="respond_to" value="reporter">
	<span><?php esc_html_e( 'Reporter', 'wcvendors-pro' ); ?></span>
</div>

<div class="wcv-form-group">
	<label class="form-label"><?php esc_html_e( 'Delete product?', 'wcvendors-pro' ); ?></label>
	<input type="checkbox" class="input" name="delete_product" value="yes">
	<span> <?php esc_html_e( 'Delete', 'wcvendors-pro' ); ?></span>
</div>

<div class="wcv-form-group">
	<label class="form-label"><?php esc_html_e( 'Product', 'wcvendors-pro' ); ?></label>
	<span> <?php echo esc_html( $product->get_name() ); ?></span>
</div>

<div class="wcv-form-group">
	<label class="form-label"><?php esc_html_e( 'Notes', 'wcvendors-pro' ); ?></label>
	<textarea name="respond_note" rows="5" class="widefat"></textarea>
<div class="wcv-form-group">

<input type="hidden" name="report_id" value="<?php echo esc_attr( $report_data->report_id ); ?>">
