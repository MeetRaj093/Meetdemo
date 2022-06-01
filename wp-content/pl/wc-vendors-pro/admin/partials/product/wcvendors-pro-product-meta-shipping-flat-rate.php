<?php

/**
 * The product flat rate shipping panels
 *
 * This file is used to display vendor shipping flat rate on the product edit page
 *
 * @link       http://www.wcvendors.com
 * @since      1.3.4
 * @version    1.8.0
 *
 * @package    WCVendors_Pro
 * @subpackage WCVendors_Pro/admin/partials/product
 */

?>

<!-- National Rates -->
<div class="options_group">
	<p><strong><?php _e( 'National rates', 'wcvendors-pro' ); ?></strong></p>

	<p class="form-field">
		<label for="_shipping_fee_national"><?php _e( 'National shipping fee', 'wcvendors-pro' ); ?></label>
		<input type="text" class="short wc_input_decimal" style="" name="_shipping_fee_national"
			   id="_shipping_fee_national" value="<?php echo $shipping_details['national']; ?>" placeholder="0">
	</p>
	<p class="form-field">
		<label for="_shipping_fee_national_min_charge"><?php _e( 'National minimum shipping fee', 'wcvendors-pro' ); ?></label>
		<input type="text" class="short wc_input_decimal" style="" name="_shipping_fee_national_min_charge" id="_shipping_fee_national_min_charge" value="<?php echo $shipping_details['national_min_charge']; ?>" placeholder="0">
	</p>
	<p class="form-field">
		<label for="_shipping_fee_maximum_shipping_fee"><?php _e( 'National maximum shipping fee', 'wcvendors-pro' ); ?></label>
		<input type="text" class="short wc_input_decimal" style="" name="_shipping_fee_national_max_charge" id="_shipping_fee_national_max_charge" value="<?php echo $shipping_details['national_max_charge']; ?>" placeholder="0">
	</p>
	<p class="form-field">
		<label for="_shipping_fee_national_free_shipping_order"><?php _e( 'National free shipping product', 'wcvendors-pro' ); ?></label>
		<input type="text" class="short wc_input_decimal" style="" name="_shipping_fee_national_free_shipping_order" id="_shipping_fee_national_free_shipping_order" value="<?php echo $shipping_details['national_free_shipping_order']; ?>" placeholder="0">
	</p>
	<p class="form-field" style="display: block;">
		<label for="_shipping_fee_national_qty_override"><?php _e( 'Charge once', 'wcvendors-pro' ); ?></label>
		<?php $checked; ?>
		<input type="checkbox" class="checkbox" style="" name="_shipping_fee_national_qty_override"
			   id="_shipping_fee_national_qty_override" <?php checked( $shipping_details['national_qty_override'], 'yes' ); ?> />
		<span class="description"><?php _e( 'Charge once per product for national shipping, even if more than one is purchased.', 'wcvendors-pro' ); ?></span>
	</p>
	<p class="form-field" style="display: block;">
		<label for="_shipping_fee_national_free"><?php _e( 'Free national shipping', 'wcvendors-pro' ); ?></label>
		<input type="checkbox" class="checkbox" style="" name="_shipping_fee_national_free"
			   id="_shipping_fee_national_free" <?php checked( $shipping_details['national_free'], 'yes' ); ?> />
		<span class="description"><?php _e( 'National shipping is free', 'wcvendors-pro' ); ?></span>	   
	</p>

	<p class="form-field" style="display: block;">
		<label for="_shipping_fee_national_disable"><?php _e( 'Disable national shipping', 'wcvendors-pro' ); ?></label>
		<input type="checkbox" class="checkbox" style="" name="_shipping_fee_national_disable"
			   id="_shipping_fee_national_disable" <?php checked( $shipping_details['national_disable'], 'yes' ); ?> />
		<span class="description"><?php _e( 'Disable national shipping', 'wcvendors-pro' ); ?></span>
	</p>

</div>

<!-- International Rates -->
<div class="options_group">

	<p><strong><?php _e( 'International Rates', 'wcvendors-pro' ); ?></strong></p>
	<p class="form-field">
		<label for="_shipping_fee_international"><?php _e( 'International shipping fee', 'wcvendors-pro' ); ?></label>
		<input type="text" class="short wc_input_decimal" style="" name="_shipping_fee_international"
			   id="_shipping_fee_international" value="<?php echo $shipping_details['international']; ?>"
			   placeholder="0">
	</p>
	<p class="form-field">
		<label for="_shipping_fee_international_min_charge"><?php _e( 'International minimum shipping fee', 'wcvendors-pro' ); ?></label>
		<input type="text" class="short wc_input_decimal" style="" name="_shipping_fee_international_min_charge" id="_shipping_fee_international_min_charge" value="<?php echo $shipping_details['international_min_charge']; ?>" placeholder="0">
	</p>
	<p class="form-field">
		<label for="_shipping_fee_international_max_charge"><?php _e( 'International maximum shipping fee', 'wcvendors-pro' ); ?></label>
		<input type="text" class="short wc_input_decimal" style="" name="_shipping_fee_international_max_charge" id="_shipping_fee_international_max_charge" value="<?php echo $shipping_details['international_max_charge']; ?>" placeholder="0">
	</p>
	<p class="form-field">
		<label for="_shipping_fee_international_free_shipping_order"><?php _e( 'International free shipping product', 'wcvendors-pro' ); ?></label>
		<input type="text" class="short wc_input_decimal" style="" name="_shipping_fee_international_free_shipping_order" id="_shipping_fee_international_free_shipping_order" value="<?php echo $shipping_details['international_free_shipping_order']; ?>" placeholder="0">
	</p>
	<p class="form-field" style="display: block;">
		<label for="_shipping_fee_international_qty_override"><?php _e( 'Charge once', 'wcvendors-pro' ); ?></label>
		<input type="checkbox" class="checkbox" style="" name="_shipping_fee_international_qty_override"
			   id="_shipping_fee_international_qty_override" <?php checked( $shipping_details['international_qty_override'], 'yes' ); ?> />
		<span class="description"><?php _e( 'Charge once per product for international shipping, even if more than one is purchased.', 'wcvendors-pro' ); ?></span>
	</p>
	<p class="form-field" style="display: block;">
		<label for="_shipping_fee_international_free"><?php _e( 'Free international shipping', 'wcvendors-pro' ); ?></label>
		<input type="checkbox" class="checkbox" style="" name="_shipping_fee_international_free"
			   id="_shipping_fee_international_free" <?php checked( $shipping_details['international_free'], 'yes' ); ?> />
		<span class="description"><?php _e( 'International shipping is free', 'wcvendors-pro' ); ?></span>
	</p>

	<p class="form-field" style="display: block;">
		<label for="_shipping_fee_international_disable"><?php _e( 'Disable international shipping', 'wcvendors-pro' ); ?></label>
		<input type="checkbox" class="checkbox" style="" name="_shipping_fee_international_disable"
			   id="_shipping_fee_international_disable" <?php checked( $shipping_details['international_disable'], 'yes' ); ?> />
		<span class="description"><?php _e( 'Disable international shipping', 'wcvendors-pro' ); ?></span>
	</p>

</div>
