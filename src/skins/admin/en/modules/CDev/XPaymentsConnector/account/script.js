/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add new card and saved cards script
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

jQuery(function () {

  shadeIframe();

  jQuery('#submit-button').click(function () {

    shadeIframe();

    var message = {
      message: 'submitPaymentForm',
      params:  {}
    };

    var iframe = jQuery('#add_new_card_iframe').get(0);

    if (window.postMessage && window.JSON) {
      iframe.contentWindow.postMessage(JSON.stringify(message), '*');
    }
  });

  jQuery(window).on('message', function (event) {

    var msg = getXpcIframeEventDataObject(event);

    if (!checkXpcIframeMessage(msg)) {
      return;
    }

    if ('showMessage' == msg.message) {
      var url = URLHandler.buildURL({ 'target': 'xpc_popup', 'type': XPC_IFRAME_DO_NOTHING, 'message': escape(msg.params.text) });
      popup.load(url);
      return;
    }

    if ('ready' == msg.message) {
      unshadeIframe();
    }

    var type = parseInt(msg.params.type);
    var message = msg.params.error;

    var height = msg.params.height;

    if (parseInt(height)) {
        jQuery('#add_new_card_iframe').css('height', parseInt(height) + 10 + 'px');
    }

    if (message
      && XPC_IFRAME_DO_NOTHING != type
    ) {

      if (XPC_IFRAME_TOP_MESSAGE != type) {
        jQuery('#add_new_card_iframe_container').hide();
      }

      core.trigger('message', { 'message': message, 'type': MESSAGE_ERROR });
    }
  });
});


// Shade iframe while it's loading 
function shadeIframe()
{
  var box = jQuery('.dialog-content');

  if (0 == jQuery(box).length) {
    return;
  }

  var overlay = jQuery('<div></div>')
    .addClass('wait-overlay')
    .appendTo(box);
  var progress = jQuery('<div></div>')
    .addClass('wait-overlay-progress')
    .appendTo(box);
  jQuery('<div></div>')
    .appendTo(progress);

  overlay.width(box.outerWidth())
    .height(box.outerHeight());

  overlay.show();
}

function unshadeIframe()
{
  jQuery('.dialog-content .wait-overlay').remove();
  jQuery('.dialog-content .wait-overlay-progress').remove();
}

