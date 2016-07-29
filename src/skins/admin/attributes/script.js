/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var ppr = popup.postprocessRequest;

core.bind('afterPopupPlace', function(event) {
  TableItemsListQueue();
  jQuery('.tooltip-main').each(
    function () {
      attachTooltip(
        jQuery('i', this),
        jQuery('.help-text', this).hide().html()
      );
    }
  );
});

core.bind('resources.ready', function(event) {
  TableItemsListQueue();
  jQuery('.tooltip-main').each(
    function () {
      attachTooltip(
        jQuery('i', this),
        jQuery('.help-text', this).hide().html()
      );
    }
  );
});

popup.postprocessRequest = function(XMLHttpRequest, textStatus, data, isValid)
{
  ppr.call(this, XMLHttpRequest, textStatus, data, isValid);
  TableItemsListQueue();

  jQuery('.select-attributetypes select').change(
    function () {
      if (jQuery(this).data('value') == jQuery(this).val()) {
        jQuery('.select-attributetypes .form-field-comment').hide();
        jQuery('li.custom-field').show();
      } else {
        jQuery('.select-attributetypes .form-field-comment').show();
        jQuery('li.custom-field').hide();
      }
    }
  );

  jQuery('.tooltip-main').each(
    function () {
      attachTooltip(
        jQuery('i', this),
        jQuery('.help-text', this).hide().html()
      );
    }
  );

  jQuery('.ajax-container-loadable form.attribute', this.base).commonController('submitOnlyChanged', false);
};

CommonForm.elementControllers.push(
  {
    pattern: '.line .input-field-wrapper.switcher.switcher-read-write.input-checkbox-addtonew',
    handler: function () {
      var input  = jQuery(':checkbox', this);
      var widget = jQuery('.widget', this);
      var fa     = jQuery(jQuery('.fa', this), '.create-line');
      var widgetOn  = 'fa-check-circle';
      var widgetOff = 'fa-check-circle-o';

      fa.removeClass('fa-power-off')
        .addClass(input.prop('checked') ? widgetOn : widgetOff);

      widget.click(
        function () {
          if (!input.prop('disabled')) {
            fa.removeClass(widgetOn + ' ' + widgetOff);

            if (!input.prop('checked')) {
              fa.addClass(widgetOff);
            } else {
              fa.addClass(widgetOn);
            }
          }
        }
      );

    }
  }
);

jQuery().ready(
  function () {

    jQuery('button.manage-groups').click(
      function () {
        var product_class_id = jQuery(this).parent().data('class-id')
          ? jQuery(this).parent().data('class-id')
          : 0;
        return !popup.load(
          URLHandler.buildURL({
            target:             'attribute_groups',
            product_class_id:   product_class_id,
            widget:             'XLite\\View\\ItemsList\\Model\\AttributeGroup'
          })
        );
      }
    );

    jQuery('button.new-attribute, .items-list.attributes .entity-edit-link a').click(
      function () {
        var product_class_id = jQuery(this).parent().data('class-id')
          ? jQuery(this).parent().data('class-id')
          : 0;
        return !popup.load(
          URLHandler.buildURL({
            target:             'attribute',
            product_class_id:   product_class_id,
            id:                 jQuery(this).parent().data('id'),
            widget:             'XLite\\View\\Attribute'
          })
        );
      }
    );

    core.bind(
      'updateattribute',
      function() {
        if (!jQuery('.ui-dialog form.attribute button.next').length) {
          self.location.reload();
        }
      }
    );

    core.bind(
      'updateattributegroups',
      function() {
        self.location.reload();
      }
    );

  }
);
