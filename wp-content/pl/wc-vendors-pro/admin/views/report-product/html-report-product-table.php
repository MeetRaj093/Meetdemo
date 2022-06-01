<div class="wrap">
	<h2>
		<?php esc_html_e( 'Report product', 'wcvendors-pro' ); ?>
	</h2>
	<form method="POST">
		<?php
		$report_product_table->prepare_items();
		$report_product_table->display();
		?>
	</form>
	<form id="report-product-response-form" method="POST">
		<div class="wcv-shade wcv-modal" id="wcv-report-product-popup">
			<div class="wcv-modal-wrapper">
				<div class="wcv-modal-header">
					<h3><?php esc_html_e( 'Report product response', 'wcvendors-pro' ); ?></h3>
					<div class="wcv-modal-close">
						<svg class="wcv-icon wcv-icon-sm">
							<use xlink:href="<?php echo WCV_PRO_PUBLIC_ASSETS_URL; ?>svg/wcv-icons.svg#wcv-icon-times"></use>
						</svg>
					</div>
				</div>
				<div class="wcv-modal-content" id="wcv-report-product-popup-content"></div>
				<div class="wcv-modal-footer">
					<input type="hidden" name="action" value="respond">
					<?php wp_nonce_field( 'wcv-report-product-action-nonce' ); ?>
					<button type="submit" id="report-product-submit" class="button button-primary button-large"><?php esc_html_e( 'Submit', 'wcvendors-pro' ); ?></button>
					<button type="button" class="button button-large wcv-modal-close"><?php esc_html_e( 'Close', 'wcvendors-pro' ); ?></button>
				</div>
			</div>
		</div>
	</form>
</div>