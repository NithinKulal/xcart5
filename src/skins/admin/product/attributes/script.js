/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var idx = 1;

jQuery().ready(
  function () {
    jQuery('.create-tpl button.remove,.new button.remove').click(
      function () {
        var box = jQuery(this).parents('ul').eq(0);
        jQuery(this).parents('li.line').eq(0).remove();
        if (0 == box.find('li.line').length) {
           box.parent().addClass('empty');
        }
      }
    );

    jQuery('.multiple-checkbox input').change(
      function () {
        if (jQuery(this).prop('checked')) {
          jQuery(this).parent().parent().addClass('multiple');

        } else {
          jQuery(this).parent().parent().removeClass('multiple');
        }
      }
    );

    jQuery('.modifiers input[type=text]').regexMask(/^[\-\+]{1}([0-9]*)([\.,]([0-9]*))?([%]{1})?$/);

    jQuery('.modifiers a').click(
      function () {
        var p = jQuery(this).parent();
        if (p.hasClass('open')) {
              changeModifiers(p)

        } else {
          jQuery('.modifiers.open').each(
            function () {
              changeModifiers(jQuery(this))
            }
          );
          p.addClass('open');
        }

        return false;
      }
    );

    jQuery('.type-s .values .new').bind(
      'keyup change focusin dblclick',
      function (event) {
        var line = jQuery(this);
        if (
          line.hasClass('new')
          && (
            line.find('input[type=text]').val()
            || (
              event.relatedTarget
              && event.relatedTarget.className
              && event.relatedTarget.className.indexOf('ui-corner-all') >= 0
            )
            || event.type == 'dblclick'
          )
        ) {
          var newLine = line.clone(true);
          newLine.find('input[type=text]').each(
            function () {
              jQuery(this).val('');
            }
          );

          idx = idx + 1;
          var oldId = '';
          var newId = '';
          var autoOption = false;
          line.find(':input').each(
            function () {
              if (this.id) {
                if (jQuery(this).hasClass('combobox')) {
                  oldId = this.id;
                }
                this.id = this.id.replace(/-new-id/, '-n' + idx);
                if (jQuery(this).hasClass('combobox')) {
                  newId = this.id;
                  autoOption = jQuery(this).autocomplete('option');
                }
              }
              this.name = this.name.replace(/\[NEW_ID\]/, '[' + (-1 * idx) + ']');
            }
          )
          line.removeClass('new').addClass('create-line');
          line.parent().append(newLine);
          core.trigger('attributes.modifiers.new', { element: newLine })
          if (autoOption) {
            var newInput = jQuery('#' + oldId).clone();
            newInput.attr('id', 'new_' + oldId)
            newInput.insertAfter('#' + oldId);
            jQuery('#' + oldId).remove();
            jQuery('#new_' + oldId).attr('id', oldId).autocomplete(autoOption);
            jQuery('#' + newId).autocomplete(autoOption);
          }
          line.parents('form').get(0).commonController.bindElements();
        }
      }
    );

    jQuery('.modifiers .popup').click(
      function (event) {
        event.stopPropagation();
      }
    );

    jQuery(document).click(
      function () {
        jQuery('.modifiers.open').each(
          function () {
            changeModifiers(jQuery(this))
          }
        );
      }
    );

    jQuery('select#save-mode').change(
      function () {
        if ('globaly' == jQuery(this).val()) {
          jQuery('form.attrs').addClass('view-changes');

        } else {
          jQuery('form.attrs').removeClass('view-changes');
        }
      }
    );

    jQuery('select,input,textarea').bind('change keyup focusin',
      function () {
        var changed = this.initialValue != this.value;
        var el = jQuery(this);
        if (changed) {
          el.addClass('is-changed').parents('li.line.value').addClass('is-changed');

        } else {
          el.removeClass('is-changed').parents('li.line.value').removeClass('is-changed');
        }
      }
    );

    jQuery('form.attrs').change(
      function () {
        if (jQuery(this).hasClass('changed')) {
          jQuery('select#save-mode').removeProp('disabled');

        } else {
          jQuery('select#save-mode').prop('disabled', 'disabled');
        }
      }
    );
  }
);

function changeModifiers(p) {
  var str = '';
  p.removeClass('open');
  p.find('input[type=text]').each(
    function () {
      if (jQuery(this).val()) {
        str = str + ' <span class="' + jQuery(this).data('type') + '-modifier">' + jQuery(this).val() + '</span>';
      }
    }
  );

  p.find('.default input[type=checkbox]:checked').each(
    function () {
      var def = jQuery(this);
      str = def.data('title') + (str ? ', ' : '') + str;
      p.parent().parent().parent().find('.modifiers').each(
        function () {
          var m = jQuery(this);
          m.find('.default input[type=checkbox]:checked').each(
            function () {
              if (jQuery(this).attr('name') != def.attr('name')) {
                jQuery(this).prop('checked', '');
                var text = m.find('span.text').html();
                text = text.replace(def.data('title') + ', ', '');
                text = text.replace(def.data('title'), '');
                m.find('span.text').html(text);
              }
            }
          );
        }
      );
    }
  );

  p.find('span.text').html(str);
  core.trigger('attributes.modifiers.change', { element: p })
}

function addAttribute(type, listId) {
  var box = jQuery('#list' + listId);

  idx = idx + 1;
  var line = jQuery('.create-tpl').clone(true);
  line
    .show()
    .removeClass('create-tpl')
    .addClass('create-line')
    .addClass('line')
    .find('.attribute-value').each(
      function () {
        if (!jQuery(this).hasClass('type-' + type.toLowerCase())) {
          jQuery(this).remove();
        }
      }
    );
   line.find(':input').each(
      function () {
        if (this.id) {
          this.id = this.id.replace(/-new-id/, '-n' + idx);
        }
        this.name = this.name.replace(/\[NEW_ID\]/, '[' + (-1 * idx) + ']');
        this.value = this.value.replace(/NEW_LIST_ID/, listId);
        this.value = this.value.replace(/NEW_TYPE/, type);
      }
    );

  box.append(line);
  line.parents('form').get(0).commonController.bindElements();
  box.parent().removeClass('empty');

  var form = box.parents('form').get(0);
  if (form) {
    form.commonController.bindElements();
  }
}
