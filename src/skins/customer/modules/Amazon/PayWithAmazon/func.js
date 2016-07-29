/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Js
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind('afterPopupPlace', function () {
  func_amazon_pa_put_button('payWithAmazonDiv_add2c_popup_btn');
  func_amazon_pa_put_button('payWithAmazonDiv_mini_cart_btn');
});

core.bind('cart.main.loaded', function () {
  func_amazon_pa_put_button('payWithAmazonDiv_cart_btn');
});

core.bind('minicart.loaded', function () {
  func_amazon_pa_put_button('payWithAmazonDiv_mini_cart_btn');
});

jQuery(function () {
  func_amazon_pa_put_button('payWithAmazonDiv_cart_btn');
  func_amazon_pa_put_button('payWithAmazonDiv_co_btn');
  func_amazon_pa_put_button('payWithAmazonDiv_mini_cart_btn');

  // detect checkout page
  if (jQuery('#addressBookWidgetDiv').length > 0 && AMAZON_PA_CONST.SID) {
    core.bind('checkout.common.anyChange', function () {
      jQuery('div.step-shipping-methods form').attr('onsubmit', 'javascript: return false;');
        func_amazon_pa_check_checkout_button();
    });
    func_amazon_pa_init_checkout();
  }
});

function func_amazon_pa_lock_checkout (lock) {
  if (lock) {
    jQuery('button.place-order').addClass('disabled');
  } else {
    jQuery('button.place-order').removeClass('disabled');
  }
}

function func_amazon_pa_block_elm (elm_sel, block) {
  var element = $(elm_sel);
  if (block) {
    assignWaitOverlay(element);
  } else {
    unassignWaitOverlay(element);
  }
}

function amazonUpdateShippingList () {
  var ship_block = 'div.step-shipping-methods';

  func_amazon_pa_block_elm('div.shipping-step', true);

  jQuery.get('cart.php?target=checkout&widget=\\XLite\\View\\Checkout\\ShippingMethodsList&_='+Math.random(), function(data) {

    jQuery(ship_block).html(jQuery(data).html());
    core.autoload(ShippingMethodsView);

    window['ShippingMethodsView'].prototype.handleMethodChange = function(){};
    jQuery(ship_block).find('form').submit(function( event ) {
      return false;
    });
    jQuery(ship_block).find('form').onsubmit = function() {
        return false;
    };

    // see checkout/steps/shipping/parts/shippingMethods.js
    if (jQuery(ship_block).find("input[name*='methodId']").length > 0 || jQuery(ship_block).find("select[name*='methodId']").length > 0) {

      jQuery(ship_block).find("input[name*='methodId']").live('change', function() {
        func_amazon_pa_on_change_shipping();
      });
      jQuery(ship_block).find("select[name*='methodId']").live('change', function() {
        func_amazon_pa_on_change_shipping();
      });

      amazon_pa_address_selected = true;
    } else {
      amazon_pa_address_selected = false;
    }

    func_amazon_pa_check_checkout_button();
    func_amazon_pa_block_elm('div.shipping-step', false);

  });
}

function func_amazon_pa_check_address (orefid) {

  func_amazon_pa_lock_checkout(true);

  jQuery.post('cart.php?target=amazon_checkout', {'mode': 'check_address', 'orefid': orefid}, function (data) {

    if (data == 'error') {
      alert('ERROR: Amazon server communication error. Please check module configuration (see logs for details)');
    }

    if (amazon_pa_order_shippable) {
      amazonUpdateShippingList();
    }

    func_amazon_pa_check_checkout_button();

    // update totals and place order button
    func_amazon_pa_refresh_totals();
  });
}

function func_amazon_pa_block_place() {
  func_amazon_pa_lock_checkout(true);
  amazon_pa_place_order_enabled = false;
}

function func_amazon_pa_unblock_place() {
  func_amazon_pa_lock_checkout(false);
  amazon_pa_place_order_enabled = true;
}

function func_amazon_pa_refresh_totals(event) {
    func_amazon_pa_block_place();
    func_amazon_pa_block_elm('div.review-step', true);

    // update cart totals section
    jQuery.get('cart.php?target=checkout&widget=\\XLite\\View\\Checkout\\CartItems&_='+Math.random(), function (data) {

      jQuery('div.cart-items').html(jQuery(data).find('div').eq(0).html());
      // core.autoload(CartItemsView);
      if (typeof DiscountPanelView == 'function') {
        var view = new DiscountPanelView('.discount-coupons-panel');
        view.assignItemsHandlers(event, {isSuccess: true});
      }
      func_amazon_pa_block_elm('div.review-step', false);

      jQuery('div.cart-items div.items-row a').click(function() {
        jQuery('div.cart-items div.list').toggle();
        return false;
      });

      func_amazon_pa_check_checkout_button();
      func_amazon_pa_unblock_place();
    });

    // update place order button
    jQuery.get('cart.php?target=checkout&widget=\\XLite\\View\\Button\\PlaceOrder&_='+Math.random(), function(data) {

      jQuery('div.button-row').html(jQuery(data).html());
      // core.autoload(PlaceOrderButtonView);

      jQuery('button.place-order').click(function() {
        func_amazon_pa_place_order();
      });

      func_amazon_pa_check_checkout_button();
      func_amazon_pa_unblock_place();
    });
}

function func_amazon_pa_check_payment(orefid) {
  amazon_pa_payment_selected = true;
  func_amazon_pa_check_checkout_button();
}

function func_amazon_pa_check_checkout_button() {
  if (typeof amazon_pa_payment_selected !== 'undefined'
    && amazon_pa_payment_selected
    && (amazon_pa_address_selected || !amazon_pa_order_shippable)
  ) {
    // enable place order button
    func_amazon_pa_lock_checkout(false);
    amazon_pa_place_order_enabled = true;
  } else {
    func_amazon_pa_lock_checkout(true);
    amazon_pa_place_order_enabled = false;
  }
}

function func_amazon_pa_on_change_shipping () {
  func_amazon_pa_block_place();
  var new_sid = jQuery('div.step-shipping-methods').find("input[type='radio']:checked").val();
  if (!new_sid) {
    new_sid = jQuery('div.step-shipping-methods').find("select[name='methodId']").val();
  }
  if (new_sid) {
    func_amazon_pa_block_elm('div.shipping-step', true);

    var form = jQuery('div.step-shipping-methods').find('form.shipping-methods');
    var formController = null;
    if (form.length > 0 && form.get(0).commonController) {
      formController = form.get(0).commonController;
    };

    var postData = {
        'action':                 'shipping',
        'methodId':               new_sid
    };

    postData[xliteConfig.form_id_name] = formController
      ? formController.getFormId()
      : xliteConfig.form_id;

    jQuery.post('cart.php?target=checkout', postData, function(data, textStatus, XMLHttpRequest) {
      if (formController) {
        formController.tryRestoreCSRFToken(XMLHttpRequest);
      };
      func_amazon_pa_block_elm('div.shipping-step', false);

      func_amazon_pa_unblock_place();
      func_amazon_pa_refresh_totals();
    });
  }
}

function func_amazon_pa_place_order () {

  if (!amazon_pa_place_order_enabled) {
    return false;
  }

  // prevent double submission
  amazon_pa_place_order_enabled = false;

  // submit form
  func_amazon_pa_block_elm('body', true);
  var co_form = jQuery('div.review-step form.place');
  co_form.removeAttr('onsubmit');
  co_form.attr('action', 'cart.php?target=amazon_checkout');
  co_form.find("input[name='target']").val('amazon_checkout');
  co_form.append('<input type="hidden" name="amazon_pa_orefid" value="'+amazon_pa_orefid+'" />');
  co_form.append('<input type="hidden" name="mode" value="place_order" />');
  return true;
}

function func_amazon_pa_init_checkout() {

  // Load
  core.bind(
    'updateCart',
    function (event) {
      func_amazon_pa_refresh_totals(event);
    }
  );

  if (jQuery.blockUI) {
    jQuery.blockUI.defaults.baseZ = 200000;
  }

  // except mobile
  if (jQuery('button.place-order').length > 0 && AMAZON_PA_CONST.SID && !AMAZON_PA_CONST.MOBILE) {

    func_amazon_pa_lock_checkout(true);

    // place order button 
    jQuery('button.place-order').click(function() {
      func_amazon_pa_place_order();
    });

    // have coupon link
    jQuery('div.coupons div.new a').click(function() {
      jQuery('div.coupons div.add-coupon').toggle();
      return false;
    });

    // tmp fix for pre-selected payment method
    jQuery('.payment-tpl').remove();
  }

  new OffAmazonPayments.Widgets.AddressBook({
    sellerId: AMAZON_PA_CONST.SID,
    amazonOrderReferenceId: amazon_pa_orefid,

    onAddressSelect: function(orderReference) {
      func_amazon_pa_check_address(amazon_pa_orefid);
    },

    design: {
      size : {width:'400px', height:'260px'}
    },

    onError: function(error) {
      if (AMAZON_PA_CONST.MODE == 'test') {
        alert("Amazon AddressBook widget error: code="+error.getErrorCode()+' msg='+error.getErrorMessage());
      }
    }

  }).bind("addressBookWidgetDiv");

  new OffAmazonPayments.Widgets.Wallet({
    sellerId: AMAZON_PA_CONST.SID,
    amazonOrderReferenceId: amazon_pa_orefid,

    design: {
      size : {width:'400px', height:'260px'}
    },

    onPaymentSelect: function(orderReference) {
      func_amazon_pa_check_payment(amazon_pa_orefid);
    },

    onError: function(error) {
      if (AMAZON_PA_CONST.MODE == 'test') {
        alert("Amazon Wallet widget error: code="+error.getErrorCode()+' msg='+error.getErrorMessage());
      }
    }
  }).bind("walletWidgetDiv");

}

function func_amazon_pa_put_button(elmid) {

  // element not found
  if (jQuery('#'+elmid).length <= 0) {
    return;
  }

  // button already created
  if (jQuery('#'+elmid).data('amz_button_placed')) {
    return;
  }
  jQuery('#'+elmid).data('amz_button_placed', true);

  new OffAmazonPayments.Widgets.Button({
    sellerId: AMAZON_PA_CONST.SID,
    useAmazonAddressBook: true,
    onSignIn: function(orderReference) {
      var amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();
      window.location = 'cart.php?target=amazon_checkout&amz_pa_ref=' + amazonOrderReferenceId;
    },
    onError: function(error) {
      if (AMAZON_PA_CONST.MODE == 'test') {
        alert("Amazon put button widget error: code="+error.getErrorCode()+' msg='+error.getErrorMessage());
      }
    }
  }).bind(elmid);
}
