<?php
/**
 * Output the report product button and popup
 */

?>

<button type="button" class="wcv-button wcv-product-reports-button" id="open-wcv-product-reports-modal" data-toggle="modal" data-target="#wcv-product-reports-modal">
	<?php echo esc_html( $button_label ); ?>
	<svg class="wcv-icon wcv-icon-sm">
			<use xlink:href="<?php echo esc_url_raw( WCV_PRO_PUBLIC_ASSETS_URL ); ?>svg/wcv-icons.svg#wcv-icon-flag"></use>
	</svg>
</button>
<div class="wcv-shade wcv-fade" id="wcv-product-reports-modal" tabindex="-1" data-trigger="#open-wcv-product-reports-modal" role="dialog" aria-labelledby="wcv-product-reports-modal-label" aria-hidden="true">
	<div class="wcv-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="wcv-product-reports-modal-label"><?php echo esc_html( $popup_title ); ?></h4>
					<button type="button" class="close" id="close-wcv-product-reports-modal" data-target="#wcv-product-reports-modal" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<form id="wcv-product-reports-form" class="wcv-form" method="POST">
						<?php do_action( 'wcvendors_product_reports_form_start' ); ?>

						<?php do_action( 'wcvendors_product_reports_before_reason_field' ); ?>
							<?php WCVendors_Pro_Product_Reports_Form::report_reason_field(); ?>
						<?php do_action( 'wcvendors_product_reports_after_reason_field' ); ?>

						<?php do_action( 'wcvendors_product_reports_before_note_field' ); ?>
							<?php WCVendors_Pro_Product_Reports_Form::report_note_field(); ?>
						<?php do_action( 'wcvendors_product_reports_after_note_field' ); ?>

						<?php
							WCVendors_Pro_Product_Reports_Form::product_reports_nonce_field();
						?>

						<?php do_action( 'wcvendors_product_reports_before_submit_button' ); ?>
							<?php WCVendors_Pro_Product_Reports_Form::product_reports_submit_button(); ?>
						<?php do_action( 'wcvendors_product_reports_after_submit_button' ); ?>

						<?php do_action( 'wcvendors_product_reports_form_end' ); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
