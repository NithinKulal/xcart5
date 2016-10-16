/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * X-Payments iframe and checkout
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function xpcMessageListener(event)
{

  if (typeof event.originalEvent == 'undefined') {
    event.originalEvent = event;
  }

  var msg = getXpcIframeEventDataObject(event);

  if (!checkXpcIframeMessage(msg)) {
    return;
  }

  if ('paymentFormSubmit' == msg.message) {
    core.trigger('checkout.common.ready', event);
    return;
  }

  if ('showMessage' == msg.message) {
    var url = URLHandler.buildURL({ 'target': 'xpc_popup', 'type': XPC_IFRAME_DO_NOTHING, 'message': escape(msg.params.text) });
    popup.load(url);
    return;
  }

  if (msg.params.height) {
    jQuery("#xpc").css('height', msg.params.height);
  }

  var type = parseInt(msg.params.type);
  var message = msg.params.error;

  if (
    message 
    && XPC_IFRAME_DO_NOTHING != type
  ) {

    if (XPC_IFRAME_TOP_MESSAGE == type) {

      core.trigger('message', { 'message': message, 'type': MESSAGE_ERROR });

    } else {

      if (
        XPC_IFRAME_CHANGE_METHOD == type
        || XPC_IFRAME_CLEAR_INIT_DATA == type
        || XPC_IFRAME_ALERT == type
      ) {
        xpcPopupError = true;
        jQuery('.xpc-box').hide();
      }

      var url = URLHandler.buildURL({ 'target': 'xpc_popup', 'type': type, 'message': escape(message) }); 

      popup.load(url);
    }
  }
  if (msg.params.returnURL) {

    jQuery(location).attr('href', msg.params.returnURL);

  } else {
    xpcLoading = false;
    xpcShadeFlag = false;
    jQuery('.save-card-box').show();
    Checkout.instance.finishLoadAnimation();
    core.trigger('checkout.common.unblock');
    core.trigger('checkout.common.anyChange');
  }

};

var xpcLoading = false;
var xpcPopupError = false;

function isXpcIframe()
{
  var result = false;

  var paymentId = Checkout.instance.getState().order.payment_method;

  if (
      typeof xpcPaymentIds != 'undefined'
      && xpcPaymentIds
      && xpcPaymentIds[paymentId]
      && xpcUseIframe
  ) {
    result = true;
  }

  return result;

}

function submitXpcIframe(event, state)
{
  if (!isXpcIframe()) {
    return false;
  }

  state.state = false;

  saveCheckoutFormDataXpc('#order_note', '#save-card');

  if (jQuery('.xpc_iframe').length) {
    var message = {
      message: 'submitPaymentForm',
      params:  {}
    };

    var xpcShown = jQuery('.xpc_iframe').get(0);

    setTimeout(Checkout.instance.startLoadAnimation, 0);
    core.trigger('checkout.common.block');

    jQuery('#status-messages').hide();

    if (window.postMessage && window.JSON) {
      xpcShown.contentWindow.postMessage(JSON.stringify(message), '*');
    }

    return false;
  }

}

function reloadXpcIframe()
{

  if (
    typeof Checkout.instance == 'undefined'
    || !isXpcIframe()
  ) {
    return false;
  }

  var paymentId = Checkout.instance.getState().order.payment_method;

  var iframe = jQuery('.xpc_iframe:visible');

  if (iframe.length == 0) {
    // Iframe not found or not visible
    return;
  }
  var src = iframe.attr('src');

  if (!src) {
    src = iframe.data('src');
  }

  iframe.attr('src', src);

  Checkout.instance.startLoadAnimation();
  core.trigger('checkout.common.block');

}

// Bind listener for messages from iframe
jQuery(window).bind('message', _.bind(xpcMessageListener, this));

// Reload iframe if payment method is changed
core.bind(['checkout.paymentTpl.loaded', 'checkout.xpc.paymentPage.loaded'], reloadXpcIframe);

// Redefines submit action
core.bind('checkout.common.ready', submitXpcIframe);

core.bind(
  'xpcevent',
  function(event, data) {

    // Process "Use saved card" box
    if (data.showSaveCardBox == 'Y') {
      jQuery('.save-card-box').show();
      jQuery('.save-card-box-no-iframe').show();
    } else {
      jQuery('.save-card-box').hide();
      jQuery('.save-card-box-no-iframe').hide();
    }

    // Process payment template box
    if (data.checkCheckoutAction == 'Y') {
      jQuery('.xpc-box').show();
      reloadXpcIframe();
    } else {
      jQuery('.xpc').hide();
    }

  }
);


// Load iframe only when payment page is opened
Checkout.require(['Checkout.StoreSections'], function() {
  var parent_SWITCH_SECTION = Checkout.StoreSections.mutations.SWITCH_SECTION;

  Checkout.StoreSections.mutations.SWITCH_SECTION = function (state, name) {
    parent_SWITCH_SECTION.apply(this, arguments);

    if (name === 'payment') {
      // Timeout is required to place function in queue
      // and execute after page content is displayed
      setTimeout(function (event, data) { core.trigger('checkout.xpc.paymentPage.loaded'); }, 0);
    }
  }
});

function switchAddress(addressId)
{
  core.post(
    'cart.php?target=checkout', 
    function() 
    {
      $('.webui-popover').hide();
    },
    {
      action: 'set_card_billing_address', 
      addressId: addressId
    }
  );
}

function popupAddress(e)
{
  jQuery('.webui-popover').hide();

  if (!e || !e.length) {
    return;
  }

  var addressId = e.attr('data-address-id');
  var cardId = e.attr('data-card-id');

  if (
    !cardId
    || !addressId
    || addressId == xpcBillingAddressId
  ) {
    return;
  }

  var content = jQuery('#popup-address-' + cardId).html(); 

  var opts = {
    title: 'Billing address',
    placement: 'top',
    closeable: true,
    cache: false,
    trigger:'manual',
    width: '300px',
    content: content
  };

  e.webuiPopover(opts);
  e.webuiPopover('show');
}

function getLabelForCard(e)
{
  var cardId = jQuery(e).val();
  return jQuery('#saved-card-label-' + cardId);
}

jQuery(document).ready(function () {
  jQuery("[name='payment[saved_card_id]']").change( 
    function() 
    {
      var label = getLabelForCard(this); 
      popupAddress(label);
    }
  );
});

