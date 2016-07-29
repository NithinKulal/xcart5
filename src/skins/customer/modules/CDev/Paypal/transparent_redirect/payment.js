/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Transparent redirect
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var shadeCheckout = function() {
  var isNewCheckout = typeof Checkout !== 'undefined'
                      && typeof Checkout.instance !== 'undefined';
  if (jQuery('.checkout-block .steps').length
    && !_.isUndefined(jQuery('.checkout-block .steps').get(0).loadable)
  ) {
    jQuery('.checkout-block .steps').get(0).loadable.shade();
  } else if (isNewCheckout) {
    Checkout.instance.startLoadAnimation();
  };
};

core.bind(
  'checkout.main.initialize',
  function() {

    core.bind(
      'checkout.common.ready',
      function(event, state) {
        var box = jQuery('.transparent-redirect-box');
        if (box.length && !box.get(0).tansparentRedirect) {
          var form = jQuery('form.place');
          form.commonController('enableBackgroundSubmit', function () {
            _.defer(shadeCheckout);
          }, function (e, params) {
            core.preprocessResponse(params.XMLHttpRequest);
            core.trigger('checkout.common.nonready');
          });
          form.commonController().submit();

          box.get(0).tansparentRedirect = true;
          state.state = false;
        }
      }
    );

    core.bind('checkout.common.anyChange', function() {
      var box = jQuery('.transparent-redirect-box');
      if (box.length) {
        var ccName = box.find('#cc_name');

        if (ccName.length && '' === ccName.val()) {
          var firstname = jQuery('#shippingaddress-firstname').val();
          var lastname = jQuery('#shippingaddress-lastname').val();

          if (!jQuery('#same_address').prop('checked')) {
            firstname = jQuery('#billingaddress-firstname').val();
            lastname = jQuery('#billingaddress-lastname').val();
          }

          box.find('#cc_name').val(firstname + ' ' + lastname);
        }

        box.find('#cc_cvv2').toggleClass('field-required', true);
      }
    });
  }
);

core.bind('updatepaypaltransparentredirect', function () {
  var isNewCheckout = typeof Checkout !== 'undefined'
                      && typeof Checkout.instance !== 'undefined';
  if (jQuery('.payment-tpl').length
    && !_.isUndefined(jQuery('.payment-tpl').get(0).loadable)
  ) {
    jQuery('.payment-tpl').get(0).loadable.load()
  } else if (isNewCheckout) {
    Checkout.instance.reloadBlock('payment-methods');
  };
});

core.bind('paypaltransparentredirect', function (e, params) {
  _.defer(shadeCheckout);
  var appendInput = function (form, name, value) {
    var input = document.createElement('input');
    input.name = name;
    input.value = value;
    form.appendChild(input);
  };

  var box = jQuery('.transparent-redirect-box');

  var expMonth = parseInt(box.find('#cc_expire_month').val());
  var expYear  = parseInt(box.find('#cc_expire_year').val().slice(-2));

  var paramList = 'TENDER=C'
    + '&ACCT=' + parseInt(box.find('#cc_number').val().replace(/\D/g, ''))
    + '&EXPDATE=' + (expMonth.length != 2 ? ('0' + expMonth) : expMonth) + expYear
    + '&CVV2=' + parseInt(box.find('#cc_cvv2').val().replace(/\D/g, ''));

  var form = document.createElement('form');
  form.action = params.action;
  form.method = 'post';

  appendInput(form, 'SECURETOKEN', params.token);
  appendInput(form, 'SECURETOKENID', params.secureTokenId);
  appendInput(form, 'PARMLIST', paramList);

  form.submit();
});
