/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

slidebar.prototype.options = _.extend(slidebar.prototype.options, {
  offCanvas: _.extend(slidebar.prototype.options.offCanvas, {
    position: "right",
    zposition: "front"
  }),
  navbar: _.extend(slidebar.prototype.options.navbar, {
    add: true,
    title: '',
  }),
  navbars: [
    {
      position: "top",
      content: ["prev", "title", "close"],
      height: 1
    }
  ]
});

core.bind('mm-menu.before_create', function(event, element) {
  if (element.find('#settings-panel ul').length) {
    element.find('#settings-panel ul').addClass('Inset');
  };
});

core.bind('mm-menu.created', function(event, api){
  api.bind('openPanel', function ($panel) {
    if ($panel.is('.mm-panel:first')) {
      $panel.parent('#slidebar').addClass('first-opened');
    } else {
      $panel.parent('#slidebar').removeClass('first-opened');
    };
  });
  api.bind('open', function () {
    jQuery('#slidebar').addClass('first-opened');
  });
});
