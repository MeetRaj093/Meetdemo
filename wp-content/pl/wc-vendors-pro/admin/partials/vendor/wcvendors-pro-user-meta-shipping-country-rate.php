<?php

/**
 * The admin country rate shipping for user and product pages
 *
 * This file is used to display the shipping type override in the edit user screen
 *
 * @link       http://www.wcvendors.com
 * @since      1.3.3
 * @version    1.8.0
 *
 * @package    WCVendors_Pro
 * @subpackage WCVendors_Pro/admin/partials/store
 */

$all_continets = WC()->countries->get_continents();
$all_countries = ( WC()->countries->get_shipping_countries() ) ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();
$row_count     = 0;
$regions       = array();
$allow_ewe     = false;
if ( 'specific' !== get_option( 'woocommerce_ship_to_countries' ) ) {
	$regions = $all_continets;
	if ( ! isset( $all_countries['EWE'] ) ) {
		$all_countries['EWE'] = __( 'Everywhere else', 'wcvendors-pro' );
		$allow_ewe            = true;
	}
} else {
	$regions = WC()->countries->get_shipping_continents();
}
?>

	<!-- Country Rate Table -->
<?php
if ( isset( $user ) ) {
	do_action( 'wcv_admin_user_before_country_rate_shipping', $user );
}
?>
	<div class="wcv-country_rate_shipping wcv-shipping-rates wcv-shipping-country">
		<?php if ( $screen->id == 'user-edit' ) : ?>
			<h3><?php _e( 'Country Rate Shipping', 'wcvendors-pro' ); ?></h3>
		<?php endif; ?>

		<div id="shipping">
			<div class="form-field wcv_shipping_rates">
				<table>
					<thead>
					<tr>
						<th class="sort">&nbsp;</th>
						<th align="left"><?php _e( 'Region / Country', 'wcvendors-pro' ); ?></th>
						<th align="left"><?php _e( 'State', 'wcvendors-pro' ); ?> </th>
						<th align="left"><?php _e( 'Postcode', 'wcvendors-pro' ); ?> </th>
						<th align="left"><?php _e( 'Fee', 'wcvendors-pro' ); ?></th>
						<th align="left"><?php _e( 'Override', 'wcvendors-pro' ); ?></th>
						<th>&nbsp;</th>
					</tr>
					</thead>
					<tbody>

					<?php if ( $shipping_rates ) : ?>
						<?php foreach ( $shipping_rates as $rate ) : ?>

							<!-- required for pro 1.4 and above -->
							<?php
							if ( ! array_key_exists( 'qty_override', $rate ) ) {
								$rate['qty_override'] = '';
							}
							?>
							<?php
							if ( ! array_key_exists( 'postcode', $rate ) ) {
								$rate['postcode'] = '';
							}
							?>

							<tr>
								<td class="sort"><i class="wcv-icon wcv-icon-sort"></i></td>
								<td class="country">
									<select name="_wcv_shipping_countries[]" id="_wcv_shipping_countries[]"
											class="country_to_state country_select">
										<option value=""><?php _e( 'Select a country or region&hellip;', 'wcvendors-pro' ); ?></option>

										<?php
										foreach ( $regions as $key => $region ) {
											$countries = array_intersect( array_keys( $all_countries ), $region['countries'] );
											echo '<option value="continent:' . esc_attr( $key ) . '" ' . selected( esc_attr( 'continent:' . $rate['region'] ), 'continent:' . $key, false ) . '>' . esc_html( $region['name'] ) . '</option>';
											foreach ( $countries as $ckey ) {
												echo '<option value="country:' . esc_attr( $ckey ) . '" ' . selected( esc_attr( 'country:' . $rate['country'] ), 'country:' . $ckey, false ) . '>' . esc_html( '&nbsp;&nbsp;' . $all_countries[ $ckey ] ) . '</option>';
											}
										}
										echo $allow_ewe ? '<option value="country:EWE" ' . selected( esc_attr( 'country:' . $rate['country'] ), 'country:EWE', false ) . '>' . __( 'Everywhere else', 'wcvendors-pro' ) . '</option>' : '';
										?>

									</select>

								</td>
								<td class="state"><input type="text"
														 placeholder="<?php _e( 'State', 'wcvendors-pro' ); ?>"
														 class="shipping_state" name="_wcv_shipping_states[]"
														 value="<?php echo esc_attr( $rate['state'] ); ?>"/></td>
								<td class="postcode"><input type="text"
															placeholder="<?php _e( 'Postcode', 'wcvendors-pro' ); ?>"
															name="_wcv_shipping_postcodes[]"
															value="<?php echo esc_attr( $rate['postcode'] ); ?>"/></td>
								<td class="fee"><input type="text" data-rules="decimal"
													   data-error="<?php _e( 'This should be a number.', 'wcvendors-pro' ); ?>"
													   placeholder="<?php _e( 'Fee', 'wcvendors-pro' ); ?>"
													   name="_wcv_shipping_fees[]"
													   value="<?php echo esc_attr( $rate['fee'] ); ?>"/></td>
								<td class="override"><input type="checkbox"
															name="_wcv_shipping_overrides[]" <?php checked( $rate['qty_override'], 'yes' ); ?> />
									<label><?php _e( 'QTY', 'wcvendors-pro' ); ?></label></td>
								<td width="1%">
									<a href="#" class="delete">
										<svg class="wcv-icon wcv-icon-sm">
												<use xlink:href="<?php echo WCV_PRO_PUBLIC_ASSETS_URL; ?>svg/wcv-icons.svg#wcv-icon-times"></use>
										</svg>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					</tbody>
					<tfoot>
					<tr>
						<th colspan="7">
							<br/>
							<a href="#" class="button insert" style="float: left;" data-row="
							<?php
							$rate = array(
								'region'       => '',
								'country'      => '',
								'state'        => '',
								'postcode'     => '',
								'fee'          => '',
								'qty_override' => '',
							);

							$file_data_row = '<tr>
										<td class="sort"><svg class="wcv-icon wcv-icon-sm"><use xlink:href="' . WCV_PRO_PUBLIC_ASSETS_URL . 'svg/wcv-icons.svg#wcv-icon-sort"></use></svg></td>
										<td class="country" data-title="' . __( 'Country', 'wcvendors-pro' ) . '">
										<select name="_wcv_shipping_countries[]" id="_wcv_shipping_countries[]" class="country_to_state country_select">
											<option value="">' . __( 'Select a country or region&hellip;', 'wcvendors-pro' ) . '</option>';
							foreach ( $regions as $key => $region ) {
								$countries      = array_intersect( array_keys( $all_countries ), $region['countries'] );
								$file_data_row .= '<option value="continent:' . esc_attr( $key ) . '" ' . selected( esc_attr( $rate['country'] ), $key, false ) . '>' . $region['name'] . '</option>';
								foreach ( $countries as $ckey ) {
									$file_data_row .= '<option value="country:' . esc_attr( $ckey ) . '" ' . selected( esc_attr( $rate['country'] ), $ckey, false ) . '>' . esc_html( '&nbsp;&nbsp;' . $all_countries[ $ckey ] ) . '</option>';
								}
							}

							$file_data_row .= $allow_ewe ? '<option value="country:EWE" ' . selected( esc_attr( 'country:' . $rate['country'] ), 'country:EWE', false ) . '>' . __( 'Everywhere else', 'wcvendors-pro' ) . '</option>' : '';

							$file_data_row .= '</select></td>

									<td class="state"><input type="text" placeholder="' . __( 'State', 'wcvendors-pro' ) . '" class="shipping_state" name="_wcv_shipping_states[]" value="' . esc_attr( $rate['state'] ) . '" /></td>
									<td class="postcode"><input type="text" placeholder="' . __( 'Postcode', 'wcvendors-pro' ) . '" name="_wcv_shipping_postcodes[]" value="' . esc_attr( $rate['postcode'] ) . '" /></td>
									<td class="fee"><input type="text" data-error="' . __( 'This should be a number.', 'wcvendors-pro' ) . '" data-rules="decimal" placeholder="' . __( 'Fee', 'wcvendors-pro' ) . '" name="_wcv_shipping_fees[]" value="' . esc_attr( $rate['fee'] ) . '" /></td>
									<td class="override"><input type="checkbox" name="_wcv_shipping_overrides[]" ' . checked( $rate['qty_override'], 'yes' ) . ' /><label>' . __( 'QTY', 'wcvendors-pro' ) . '</label></td>
									<td width="1%"><a href="#" class="delete"><svg class="wcv-icon wcv-icon-sm">
									<use xlink:href="' . WCV_PRO_PUBLIC_ASSETS_URL . 'svg/wcv-icons.svg#wcv-icon-times"></use>
							</svg></i></a></td>
								</tr>';

							echo esc_attr( $file_data_row );
							?>
							">
								<?php _e( 'Add Rate', 'wcvendors-pro' ); ?></a><br/><br/>
						</th>
					</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
<?php if ( isset( $user ) ) {
	do_action( 'wcv_admin_user_after_country_rate_shipping', $user );
} ?>
