/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Languages items list javascript controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

ItemsList.prototype.listeners.languages = function(handler)
{

  var lastDefaultCustomer = jQuery('input[type="radio"][name="defaultCustomer"]:checked');
  var lastDefaultAdmin = jQuery('input[type="radio"][name="defaultAdmin"]:checked');

  // Check if selected row can be marked as default
  function checkLanguagesDefaultRadioButton(type, current)
  {
    var main = jQuery(current).parents('tr').eq(0);
    var flag = 0;
    var switcher = main.find('.cell .action .inline-switcher input[type="checkbox"]:checked');

    if ('customer' == type) {
      prev = lastDefaultCustomer;

    } else {
      prev = lastDefaultAdmin;
    }

    // flag = 1 if switcher is not exists or enabled
    if (jQuery(switcher).length == 0) {
      switcher = main.find('.cell .action .inline-switcher input[type="checkbox"]');
      flag = (jQuery(switcher).length == 0);

    } else {
      var remove = main.find('.cell .action .remove-wrapper button.remove.mark');
      if (jQuery(remove).length == 0) {
        flag = 1;
      }
    }

    if (flag) {
      jQuery(current).prop('checked', 'checked');
      if ('customer' == type) {
        lastDefaultCustomer = current;

      } else {
        lastDefaultAdmin = current;
      }

    } else {
      jQuery(current).prop('checked', '');
      jQuery(prev).prop('checked', 'checked');
    }
  }

  // Toggle radio button visibility
  function toggleDefaultRadioButtonVisibility(elm, enable)
  {
    var main = jQuery(elm).parents('tr').eq(0);
    if (enable) {
      jQuery(elm).show();
      jQuery(main).removeClass('lock');

    } else {
      jQuery(elm).hide();
      jQuery(main).addClass('lock');
    }
  }

  // Radio buttons: default customer language
  jQuery('input[type="radio"][name="defaultCustomer"]').change(
    function() {
      checkLanguagesDefaultRadioButton('customer', this);
    }
  );

  // Radio buttons: default admin language
  jQuery('input[type="radio"][name="defaultAdmin"]').change(
    function() {
      checkLanguagesDefaultRadioButton('admin', this);
    }
  );

  // Prevent disabling the language which is marked as a default
  jQuery('.input-field-wrapper.switcher').click(
    function() {
      var main = jQuery(this).parents('tr').eq(0);
      var defC = main.find('.cell .language-defaultCustomer input[type="radio"]');
      var defA = main.find('.cell .language-defaultAdmin input[type="radio"]');

      if (!jQuery(this).hasClass('enabled')) {

        var cond = (jQuery(defC) && jQuery(defC).prop('checked'))
          || (jQuery(defA) && jQuery(defA).prop('checked'))

        if (cond) {
          var input = jQuery(':checkbox', this);
          var widget = jQuery('.widget', this);

          input.prop('checked', 'checked');
          input.get(0).setAttribute('checked', 'checked');

          jQuery(this).addClass('enabled').removeClass('disabled');
          widget.attr('title', widget.data('disable-label'));
          main.parents('form').change();

        } else {
          toggleDefaultRadioButtonVisibility(defC, 0);
          toggleDefaultRadioButtonVisibility(defA, 0);
        }

      } else {
          toggleDefaultRadioButtonVisibility(defC, 1);
          toggleDefaultRadioButtonVisibility(defA, 1);
      }
    }
  );

  // Prevent removing the language which is marked as a default
  jQuery('.cell .action .remove-wrapper').click(
    function() {

      var btn = jQuery('button.remove', this);

      if (jQuery(btn).hasClass('mark')) {
        var main = jQuery(this).parents('tr').eq(0);
        var switcher = main.find('.cell .language-defaultCustomer input[type="radio"]:checked');

        if (jQuery(switcher).length == 0) {
          switcher = main.find('.cell .language-defaultAdmin input[type="radio"]:checked');
        }

        if (jQuery(switcher).length > 0) {
          var inp = jQuery('input', this).eq(0);
          var cell = btn.parents('.line').eq(0);

          inp.removeProp('checked');
          btn.removeClass('mark');
          cell.removeClass('remove-mark');
          cell.parents('form').change();
        }
      }
    }
  );
}
