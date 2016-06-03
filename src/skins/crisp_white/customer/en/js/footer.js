/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function() {
	$('.footer-menu').collapser();
	$('.form-control').floatingLabel();

	core.bind('checkout.main.ready', function() {
		$('.checkout_fastlane_container .form-control').floatingLabel();
	});

    core.bind(['load', 'loader.loaded', 'popup.open'], function() {
        $('.form-control').floatingLabel();
    });
});