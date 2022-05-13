(function( $ ) {
	'use strict';

	 function copyToClipboard(text) {
		var $temp = jQuery('<input>');
		jQuery('body').append($temp);
		$temp.val(text).select();
		document.execCommand('copy');
		$temp.remove();
	}

	jQuery(document).ready(function() {
		jQuery('.pm_coupon_code').prepend('<i class="fa fa-paste"></i>');
		jQuery('.pm_coupon_code').attr('title', 'Click to copy code');
		jQuery('.pm_coupon_code').on('click', (event) => {
			let text = jQuery(event.target).text();
			copyToClipboard(text);
		});
	});

})( jQuery );
