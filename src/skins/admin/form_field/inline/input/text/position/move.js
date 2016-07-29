/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Move controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: 'tbody.lines',
    condition: '.inline-field.inline-move',
    handler: function (form) {

      jQuery(this).find('.inline-move').disableSelection();

      var tds = jQuery(this).find('td');

      tds.each(
        function () {
          var td = jQuery(this);
          td.data('saved-width', Math.round(td.width()));
        }
      );

      jQuery(this).sortable({
        axis:                 'y',
        handle:               '.inline-move',
        items:                'tr:not(.dump-entity)',
        cancel:               '.dump-entity, .remove-mark',
        opacity:              0.8,
        placeholder:          'sortable-placeholder',
        forcePlaceholderSize: true,
        start:                function (event, ui)
        {
          var sortablePlaceholder = jQuery('.sortable-placeholder');
          sortablePlaceholder.height(ui.item.height());

          if (ui.item.hasClass('remove-mark')) {
            ui.item.parent().sortable('cancel');

          } else {

            ui.helper.find('td').each(
              function (index) {
                var td = jQuery(this);
                td.width(td.data('saved-width'));
                sortablePlaceholder.find('td').eq(index).width(Math.round(td.innerWidth()));
              }
            );

          }
        },
        update:               function(event, ui)
        {
          ui.item.css('width', 'auto');

          // Reassign position values
          var min = 10;
          form.find('.inline-field.inline-move input').each(
            function () {
              min = parseInt(10 == min ? min : Math.min(this.value, min));
            }
          );

          form.find('.inline-field.inline-move input').each(
            function () {
              jQuery(this).attr('value', min);
              min += 10;
            }
          );

          // Change
          ui.item.parents('tbody.lines').trigger('positionChange');
          ui.item.parents('form').change();
        },
        change: function(event, ui) {
        }
      });
    }
  }
);
