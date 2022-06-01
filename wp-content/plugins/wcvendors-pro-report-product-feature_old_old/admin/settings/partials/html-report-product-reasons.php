<?php
/**
 * Report product reasons html
 *
 * @package WCVendors_Pro
 * @author WCVendors
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$data_row    = '<tr>
<td class="sort">
	<svg class="wcv-icon wcv-icon-sm"><use xlink:href="http://wcvendors.test/wp-content/plugins/wcvendors-pro/public/assets/svg/wcv-icons.svg#wcv-icon-sort"></use></svg>
</td>
<td>
	<input type="text" name="wcvendors_pro_report_product_reasons[]" value="" />
	<a href="#" class="delete">
		<svg class="wcv-icon wcv-icon-sm">
			<use xlink:href="http://wcvendors.test/wp-content/plugins/wcvendors-pro/public/assets/svg/wcv-icons.svg#wcv-icon-times"></use>
		</svg>
	</a>		
</td>
</tr>';
?>

<tr valign="top">
	<th class="titledesc" scope="row">
		<?php esc_html_e( 'Report product reasons', 'wcvendors-pro' ); ?>
	</th>
	<td class="forminp">
		<table class="report_reason_table">
			<tbody>
			<?php if ( ! empty( $reasons ) ) : ?>
				<?php foreach ( $reasons as $reason ) : ?>
					<tr>
						<td class="sort">
							<svg class="wcv-icon wcv-icon-sm"><use xlink:href="http://wcvendors.test/wp-content/plugins/wcvendors-pro/public/assets/svg/wcv-icons.svg#wcv-icon-sort"></use></svg>
						</td>
						<td>
							<input type="text" name="wcvendors_pro_report_product_reasons[]" value="<?php echo esc_attr( $reason ); ?>" />
							<a href="#" class="delete">
								<svg class="wcv-icon wcv-icon-sm">
									<use xlink:href="http://wcvendors.test/wp-content/plugins/wcvendors-pro/public/assets/svg/wcv-icons.svg#wcv-icon-times"></use>
								</svg>
							</a>		
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
				<tr>
					<td></td>
					<td>
						<a href="#" class="wcv-button button-primary insert" data-row="<?php echo esc_attr( $data_row ); ?>"><?php esc_html_e( 'Add row', 'wcvendors-pro' ); ?></a>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
