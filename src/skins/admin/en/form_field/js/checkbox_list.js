/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Multiselect microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      return 0 < this.$element.filter('select.checkbox-list').length;
    },
    handler: function () {
      var options = {
        minWidth: this.$element.width(),
        header: false,
        noneSelectedText: this.$element.data('placeholder'),
        selectedList: this.$element.data('selected-list-threshold'),
        close: function() {
          showCheckboxListValues(this);
        },
        height: 10 < this.$element.find('options').length ? 250 : 'auto'
      };

      if (this.$element.data('none-selected-text')) {
        options.noneSelectedText = this.$element.data('none-selected-text');
      }

      if (this.$element.data('selected-text')) {
        options.selectedText = this.$element.data('selected-text');
      }

      if (this.$element.data('header')) {
        options.header = true;

      } else if (this.$element.data('filter')) {
        options.header = 'close';
      }

      if (this.$element.data('filter')) {
        options.classes = 'ui-multiselect-with-filter';
      }

      this.$element.after('<div class="checkbox-list-values"></div>');
      this.$element.multiselect(options);
      showCheckboxListValues(this.$element);

      if (this.$element.data('filter')) {
        options = {placeholder: this.$element.data('filter-placeholder')};

        this.$element.multiselectfilter(options);

        jQuery('.ui-multiselect-filter').each(
          function () {
            if (3 == this.childNodes[0].nodeType) {
              this.removeChild(this.childNodes[0]);
            }
          }
        );
      }
    }
  }
);

function showCheckboxListValues(elem) {
  var value = '';
  if (jQuery(elem).data('selected-list-threshold') < jQuery(elem).find('option:selected').length) {
    value = jQuery(elem).find('option:selected').map(function() { return jQuery(this).text(); }).get().join(', ');
  }
  jQuery(elem).parent().find('div.checkbox-list-values').text(value);;
}
