jQuery(document).ready(function($) {
	$('#open-wcv-product-reports-modal').on('click', function(e) {
		e.preventDefault();
		let target = $(this).data('target');
		$(target).addClass('visible');
	});
	$('#close-wcv-product-reports-modal').on('click', function(e) {
		e.preventDefault();
		let target = $(this).data('target');
		$(target).removeClass('visible');
	});
});
