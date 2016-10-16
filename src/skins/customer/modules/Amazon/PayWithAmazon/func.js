/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Js
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('Amazon/PayWithAmazon', ['js/jquery', 'ready'], function ($) {
  var Amazon = {
    orderReference: null,

    button: function (id, type, color, size, returnUrl) {
      var el = $('#' + id);

      // element not found or button already created
      if (!el.length || el.get(0).amz_button_placed) {
        return;
      }

      el.get(0).amz_button_placed = true;

      size = el.data('size') || size;

      return OffAmazonPayments.Button(
        id,
        amazonConfig.sid,
        {
          type: type,
          color: color,
          size: size,

          authorization: function () {
            if (window.location.protocol === 'https:') {
              amazon.Login.authorize(
                {
                  scope: 'profile payments:widget payments:shipping_address',
                  popup: 'true'
                },
                returnUrl
              );
            } else {
              var loc = window.location;

              if (type == 'PwA') {
                loc.replace('https://' + loc.host + loc.pathname + URLHandler.buildQueryParams({target: 'checkout'}));
              } else {
                loc.replace('https://' + loc.host + loc.pathname + URLHandler.buildQueryParams({target: 'login'}));
              }
            }
          }
        }
      );
    },

    pwaButton: function (id) {
      return this.button(id, 'PwA', 'Gold', 'medium', URLHandler.buildURL({
        target: 'amazon_checkout',
        mode: 'payWithAmazon'
      }));
    },

    lwaButton: function (id) {
      return this.button(id, 'LwA', 'Gold', 'medium', URLHandler.buildURL({
        target: 'amazon_checkout',
        mode: 'loginWithAmazon',
        returnUrl: xliteConfig.target === 'checkout' ? '' : window.location.href
      }));
    },

    lwaIcon: function (id) {
      return this.button(id, 'A', 'Gold', 'small', URLHandler.buildURL({
        target: 'amazon_checkout',
        mode: 'loginWithAmazon',
        returnUrl: xliteConfig.target === 'checkout' ? '' : window.location.href
      }));
    },

    lockCheckout: function (lock) {
      jQuery('button.place-order').toggleClass('disabled', lock);
    },

    placeOrderEnabled: false,
    blockPlaceOrder: function () {
      this.lockCheckout(true);
      this.placeOrderEnabled = false;
    },

    unblockPlaceOrder: function () {
      this.lockCheckout(false);
      this.placeOrderEnabled = true;
    },

    blockElement: function (selector, block) {
      var element = $(selector);
      if (block) {
        assignWaitOverlay(element);
      } else {
        unassignWaitOverlay(element);
      }
    },

    initAddressBookWidget: function (id) {
      var self = this;

      var params = {
        sellerId: amazonConfig.sid,
        onOrderReferenceCreate: function (orderReference) {
          self.orderReference = orderReference.getAmazonOrderReferenceId();
          self.initWalletWidget('walletWidgetDiv');
        },
        onAddressSelect: function (orderReference) {
          self.checkAddress()
        },
        design: {
          designMode: 'responsive'
        }
      };

      new OffAmazonPayments.Widgets.AddressBook(params).bind(id);
    },

    checkAddress: function () {
      this.lockCheckout(true);
      var self = this;
      jQuery.post('cart.php?target=amazon_checkout', {
        'mode': 'check_address',
        'orefid': this.orderReference
      }, function (data) {

        if (data == 'error') {
          alert('ERROR: Amazon server communication error. Please check module configuration (see logs for details)');
        }

        if (amazonConfig.orderShippable) {
          self.updateShippingList();
        }

        self.checkCheckoutButton();

        // update totals and place order button
        self.refreshTotals();
      });
    },

    updateShippingList: function () {
      var self = this;
      var ship_block = 'div.step-shipping-methods';
      this.blockElement('div.shipping-step', true);

      jQuery.get('cart.php?target=checkout&widget=\\XLite\\View\\Checkout\\ShippingMethodsList&_=' + Math.random(), function (data) {

        jQuery(ship_block).html(jQuery(data).html());
        core.autoload(ShippingMethodsView);

        window['ShippingMethodsView'].prototype.handleMethodChange = function () {
        };

        jQuery(ship_block).find('form').submit(function (event) {
          return false;
        });
        jQuery(ship_block).find('form').onsubmit = function () {
          return false;
        };

        // see checkout/steps/shipping/parts/shippingMethods.js
        if (jQuery(ship_block).find("input[name*='methodId']").length > 0 || jQuery(ship_block).find("select[name*='methodId']").length > 0) {

          jQuery(ship_block).find("input[name*='methodId']").live('change', _.bind(self.onChangeShipping, self));
          jQuery(ship_block).find("select[name*='methodId']").live('change', _.bind(self.onChangeShipping, self));

          self.addressSelected = true;
        } else {
          self.addressSelected = false;
        }

        self.checkCheckoutButton();
        self.blockElement('div.shipping-step', false);
      });
    },

    onChangeShipping: function () {
      this.blockPlaceOrder();

      var new_sid = jQuery('div.step-shipping-methods').find("input[type='radio']:checked").val();
      if (!new_sid) {
        new_sid = jQuery('div.step-shipping-methods').find("select[name='methodId']").val();
      }
      if (new_sid) {
        this.blockElement('div.shipping-step', true);

        var form = jQuery('div.step-shipping-methods').find('form.shipping-methods');
        var formController = null;
        if (form.length > 0 && form.get(0).commonController) {
          formController = form.get(0).commonController;
        }

        var postData = {
          'action': 'shipping',
          'methodId': new_sid
        };

        postData[xliteConfig.form_id_name] = formController
          ? formController.getFormId()
          : xliteConfig.form_id;

        var self = this;
        jQuery.post('cart.php?target=checkout', postData, function (data, textStatus, XMLHttpRequest) {
          if (formController) {
            formController.tryRestoreCSRFToken(XMLHttpRequest);
          }

          self.blockElement('div.shipping-step', false);

          self.unblockPlaceOrder();
          self.refreshTotals();
        });
      }
    },

    initWalletWidget: function (id) {
      var self = this;
      new OffAmazonPayments.Widgets.Wallet({
        sellerId: amazonConfig.sid,
        amazonOrderReferenceId: self.orderReference,
        onPaymentSelect: function (orderReference) {
          self.paymentSelected = true;
          self.checkCheckoutButton();
        },
        design: {
          designMode: 'responsive'
        }
      }).bind(id);
    },

    initCheckout: function () {

      // Load
      core.bind('updateCart', _.bind(this.refreshTotals, this));

      if (jQuery.blockUI) {
        jQuery.blockUI.defaults.baseZ = 200000;
      }

      // except mobile
      if (jQuery('button.place-order').length > 0 && amazonConfig.sid && !amazonConfig.mobile) {
        this.lockCheckout(true);

        // place order button
        jQuery('button.place-order').click(_.bind(this.placeOrder, this));

        // have coupon link
        jQuery('div.coupons div.new a').click(function () {
          jQuery('div.coupons div.add-coupon').toggle();
          return false;
        });

        // tmp fix for pre-selected payment method
        jQuery('.payment-tpl').remove();
      }

      this.initAddressBookWidget('addressBookWidgetDiv');
    },

    refreshTotals: function (event) {
      var self = this;

      this.blockPlaceOrder();
      this.blockElement('div.review-step', true);

      // update cart totals section
      jQuery.get('cart.php?target=checkout&widget=\\XLite\\View\\Checkout\\CartItems&_=' + Math.random(), function (data) {

        jQuery('div.cart-items').html(jQuery(data).find('div').eq(0).html());

        // core.autoload(CartItemsView);
        if (typeof DiscountPanelView == 'function') {
          var view = new DiscountPanelView('.discount-coupons-panel');
          view.assignItemsHandlers(event, {isSuccess: true});
        }
        self.blockElement('div.review-step', false);

        jQuery('div.cart-items div.items-row a').click(function () {
          jQuery('div.cart-items div.list').toggle();
          return false;
        });

        self.checkCheckoutButton();
        self.unblockPlaceOrder();
      });

      // update place order button
      jQuery.get('cart.php?target=checkout&widget=\\XLite\\View\\Button\\PlaceOrder&_=' + Math.random(), function (data) {

        jQuery('div.button-row').html(jQuery(data).html());
        // core.autoload(PlaceOrderButtonView);

        jQuery('button.place-order').click(_.bind(self.placeOrder, self));

        self.checkCheckoutButton();
        self.unblockPlaceOrder();
      });
    },

    placeOrder: function () {
      if (!this.placeOrderEnabled) {
        return false;
      }

      // prevent double submission
      this.placeOrderEnabled = false;

      // submit form
      this.blockElement('body', true);

      var co_form = jQuery('div.review-step form.place');
      co_form.removeAttr('onsubmit');
      co_form.attr('action', 'cart.php?target=amazon_checkout');
      co_form.find("input[name='target']").val('amazon_checkout');
      co_form.append('<input type="hidden" name="amazon_pa_orefid" value="' + this.orderReference + '" />');
      co_form.append('<input type="hidden" name="mode" value="place_order" />');
      return true;
    },

    paymentSelected: false,
    addressSelected: false,

    checkCheckoutButton: function () {
      if (this.paymentSelected
        && (this.addressSelected || !amazonConfig.orderShippable)
      ) {
        this.unblockPlaceOrder();
      } else {
        this.blockPlaceOrder();
      }
    }

  };

  core.bind('afterPopupPlace', function () {
    Amazon.pwaButton('payWithAmazonDiv_add2c_popup_btn');
    Amazon.pwaButton('payWithAmazonDiv_mini_cart_btn');
  });

  core.bind('cart.main.loaded', function () {
    Amazon.pwaButton('payWithAmazonDiv_cart_btn');
  });

  core.bind('minicart.loaded', function () {
    Amazon.pwaButton('payWithAmazonDiv_mini_cart_btn');
  });

  Amazon.pwaButton('payWithAmazonDiv_cart_btn');
  Amazon.pwaButton('payWithAmazonDiv_co_btn');
  Amazon.pwaButton('payWithAmazonDiv_mini_cart_btn');

  if ($('#addressBookWidgetDiv').length > 0 && amazonConfig.sid) {
    core.bind('checkout.common.anyChange', function () {
      jQuery('div.step-shipping-methods form').attr('onsubmit', 'javascript: return false;');
      Amazon.checkCheckoutButton();
    });

    Amazon.initCheckout();
  }

  return Amazon;
});