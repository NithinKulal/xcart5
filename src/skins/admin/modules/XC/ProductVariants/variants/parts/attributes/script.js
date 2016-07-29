/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {

    jQuery('div.attributes div.checkbox').click(
      function () {
        jQuery(this).find('input[type=checkbox]').click();
        checkCreateVariants();
      }
    );

    jQuery('div.attributes div.values').click(
      function () {
        jQuery(this).parent().toggleClass('fade-variant');
      }
    );

    jQuery('div.attributes div.checkbox input').change(
      function (event) {
        if (jQuery(this).prop('checked')) {
          jQuery(this).parent().addClass('checked');
        } else {
          jQuery(this).parent().removeClass('checked');
        }
        checkCreateVariants();
      }
    ).click(
      function (event) {
        event.stopPropagation();
      }
    );

    jQuery('a.submit-form').click(
      function () {
        var flag = true;
        var mode = jQuery(this).data('mode');
        if ('create_variants' == mode) {
          if (checkCreateVariants() > maxVariantsError) {
            alert(variantMessages['limit-error']);
            flag = false;
          } else if (checkCreateVariants() > maxVariantsWarning) {
            flag = confirm(variantMessages['limit-confirmation']);
          }
        }
        if (flag) {
          jQuery('form.form-attributes input[name=action]').val(mode);
          jQuery('form.form-attributes').submit();
        }
        return false;
      }
    );

    jQuery('div.variants-are-based button').click(
      function () {
        jQuery('div.variants-are-based').hide();
        jQuery('div.variants').hide();
        jQuery('div.sticky-panel').hide();
        jQuery('div.attributes').removeClass('hidden');
        if (0 < jQuery('div.alert.variants-limit').length) {
          jQuery('div.alert.variants-limit').hide();
        }
      }
    );

    jQuery('div.attributes button.cancel').click(
      function () {
        jQuery('div.variants-are-based').show();
        jQuery('div.variants').show();
        jQuery('div.sticky-panel').show();
        jQuery('div.attributes').addClass('hidden');
        if (0 < jQuery('div.alert.variants-limit').length) {
          jQuery('div.alert.variants-limit').show();
        }
      }
    );

    checkCreateVariants();
  }
);

function checkCreateVariants() {
    var variantsCount = 1;
    jQuery('div.attributes input:checked').each(
      function() {
        variantsCount *= jQuery(this).data('count');
      }
    );

    if (variantsCount > 1) {
        jQuery('.create-variants').show();
        jQuery('div.attributes').addClass('checked');
        jQuery('div.attributes .save-changes span').text(jQuery('div.attributes .buttons').data('attributes-title'));

    } else {
        jQuery('.create-variants').hide();
        jQuery('div.attributes').removeClass('checked');
        jQuery('div.attributes .save-changes span').text(jQuery('div.attributes .buttons').data('no-attributes-title'));
    }

    jQuery('.variants-count').text('(' + variantsCount + ')');

    var variantsErrorBox = jQuery('.variants-limit-error');
    var variantsWarningBox = jQuery('.variants-limit-warning');

    if (variantsCount > maxVariantsError) {
      jQuery('a.create-variants').hide();
      jQuery('span.create-variants.static').show();
      jQuery(variantsErrorBox).attr('title', variantMessages['limit-error']);
      jQuery(variantsWarningBox).hide();
      jQuery(variantsErrorBox).show();

    } else if (variantsCount > maxVariantsWarning) {
      jQuery('a.create-variants').show();
      jQuery('span.create-variants.static').hide();
      jQuery(variantsWarningBox).attr('title', variantMessages['limit-warning']);
      jQuery(variantsWarningBox).show();
      jQuery(variantsErrorBox).hide();

    } else {
      jQuery('a.create-variants').show();
      jQuery('span.create-variants.static').hide();
      jQuery(variantsWarningBox).hide();
      jQuery(variantsErrorBox).hide();
    }

    return variantsCount;
}
