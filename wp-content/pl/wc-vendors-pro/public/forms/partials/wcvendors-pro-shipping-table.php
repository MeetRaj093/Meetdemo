<?php

/**
 * The shipping rates table
 *
 * This file is used to show the shipping rates for a product or store
 *
 * @link       http://www.wcvendors.com
 * @since      1.1.0
 * @version    1.8.0
 *
 * @package    WCVendors_Pro
 * @subpackage WCVendors_Pro/public/forms/partials/
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

<div id="shipping-country-rates" class="form-field wcv_shipping_rates">
	<table id="wcv_shipping_rates_table">
		<thead>
		<tr>
			<th class="sort">&nbsp;</th>
			<th class="country"><?php _e( 'Region / Country', 'wcvendors-pro' ); ?></th>
			<th class="state"><?php _e( 'State', 'wcvendors-pro' ); ?> </th>
			<th class="postcode"><?php _e( 'Postcode', 'wcvendors-pro' ); ?> </th>
			<th class="fee"><?php _e( 'Shipping Fee', 'wcvendors-pro' ); ?></th>
			<th class="override"><?php _e( 'Override', 'wcvendors-pro' ); ?></th>
			<th class="del">&nbsp;</th>
		</tr>
		</thead>
		<tbody>

		<?php if ( $shipping_rates ) : ?>
			
			<?php for ( $i = 0; $i < count( $shipping_rates ); $i ++ ) : ?>

				<?php $rate = $shipping_rates[ $i ]; ?>

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
					<td class="sort">
						<svg class="wcv-icon wcv-icon-sm">
							<use xlink:href="<?php echo WCV_PRO_PUBLIC_ASSETS_URL; ?>svg/wcv-icons.svg#wcv-icon-sort"></use>
						</svg>
					</td>

					<td class="country" data-title="<?php _e( 'Country', 'wcvendors-pro' ); ?>">
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
							echo $allow_ewe ?  '<option value="country:EWE" ' . selected( esc_attr( 'country:' . $rate['country'] ), 'country:EWE', false ) . '>' . __( 'Everywhere else', 'wcvendors-pro' ) . '</option>' : '';
							?>
						</select>
					</td>
					<td class="state" data-title="<?php _e( 'State', 'wcvendors-pro' ); ?>">
						<input type="text" placeholder="<?php _e( 'State', 'wcvendors-pro' ); ?>" class="shipping_state"
							   name="_wcv_shipping_states[]" value="<?php echo esc_attr( $rate['state'] ); ?>"/>
					</td>
					<td class="postcode" data-title="<?php _e( 'Postcode', 'wcvendors-pro' ); ?>">
						<input type="text" placeholder="<?php _e( 'Postcode', 'wcvendors-pro' ); ?>"
							   name="_wcv_shipping_postcodes[]" value="<?php echo esc_attr( $rate['postcode'] ); ?>"/>
					</td>
					<td class="fee" data-title="<?php _e( 'Shipping Fee', 'wcvendors-pro' ); ?>">
						<input type="text" placeholder="<?php _e( 'Fee', 'wcvendors-pro' ); ?>"
							   name="_wcv_shipping_fees[]" value="<?php echo esc_attr( $rate['fee'] ); ?>"
							   data-parsley-price/>
					</td>
					<td class="override" data-title="<?php _e( 'Override', 'wcvendors-pro' ); ?>">
						<input type="checkbox" name="_wcv_shipping_overrides_<?php echo $i; ?>"
							   id="_wcv_shipping_overrides_<?php echo $i; ?>" <?php checked( $rate['qty_override'], 'yes' ); ?> />
						<label><?php _e( 'QTY', 'wcvendors-pro' ); ?></label>
					</td>
					<td class="del">
						<a href="#" class="delete">
							<svg class="wcv-icon wcv-icon-sm">
								<use xlink:href="<?php echo WCV_PRO_PUBLIC_ASSETS_URL; ?>svg/wcv-icons.svg#wcv-icon-times"></use>
							</svg>
						</a>
					</td>
				</tr>
				<?php $row_count = $i; ?>
			<?php endfor; ?>
		<?php endif; ?>
		</tbody>

		<tfoot>
			<tr>
				<th colspan="7">
					<a href="#" class="button insert" data-row="
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
								<td class="state" data-title="' . __( 'State', 'wcvendors-pro' ) . '"><input type="text" placeholder="' . __( 'State', 'wcvendors-pro' ) . '" class="shipping_state" name="_wcv_shipping_states[]" value="' . esc_attr( $rate['state'] ) . '" /></td>
								<td class="postcode" data-title="' . __( 'Postcode', 'wcvendors-pro' ) . '"><input type="text" placeholder="' . __( 'Postcode', 'wcvendors-pro' ) . '" name="_wcv_shipping_postcodes[]" value="' . esc_attr( $rate['postcode'] ) . '" /></td>
								<td class="fee" data-title="' . __( 'Shipping Fee', 'wcvendors-pro' ) . '"><input type="text" data-parsley-price placeholder="' . __( 'Fee', 'wcvendors-pro' ) . '" name="_wcv_shipping_fees[]" value="' . esc_attr( $rate['fee'] ) . '" /></td>
								<td class="override" data-title="' . __( 'Override', 'wcvendors-pro' ) . '"><input type="checkbox" id="_wcv_shipping_overrides_" name="_wcv_shipping_overrides_" ' . checked( $rate['qty_override'], 'yes' ) . ' /><label>' . __( 'QTY', 'wcvendors-pro' ) . '</label></td>
								<td width="1%"><a href="#" class="delete"><svg class="wcv-icon wcv-icon-sm"><use xlink:href="' . WCV_PRO_PUBLIC_ASSETS_URL . 'svg/wcv-icons.svg#wcv-icon-times"></use></svg></a></td>
							</tr>';

					echo esc_attr( $file_data_row );
					?>
					"><?php _e( 'Add Rate', 'wcvendors-pro' ); ?></a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>

<!-- <td class="country"><input type="text" placeholder="'. __( "Country", "wcvendors-pro" ) .'" name="_wcv_shipping_countries[]" value="' .esc_attr( $rate["country"] ). '" /></td> -->
