/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Install modules selected
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function unsetModule(id)
{
  jQuery('#install-' + id).prop('checked', '');

  jQuery('.install-modules-button').removeClass('disabled')
  if (jQuery('.install-module-action:checked').length === 0) {
    jQuery('.install-modules-button').addClass('disabled');
  }

  jQuery('.sticky-panel button, .sticky-panel .actions').trigger(
    'select-to-install-module',
    {
      id: id,
      checked: false,
      moduleName: ''
    }
  );
}

jQuery(document).ready(function () {
  jQuery('.sticky-panel .install-modules-button').bind(
    'select-to-install-module',
    function (event, arg) {
      event.stopImmediatePropagation();
      var $this = jQuery(this);

      if (arg.checked) {
        $this.append('<input type="hidden" name="moduleIds[]" value="' + arg.id + '" id="moduleids_' + arg.id + '">');
        core.get(
          URLHandler.buildURL({'target': 'addons_list_marketplace', 'action': 'set_install', 'id': arg.id}),
          function(xhr, status, data) {
          },
          {},
          {timeout: 10000}
        );
      } else {
        jQuery('input#moduleids_' + arg.id, $this).remove();
        core.get(
          URLHandler.buildURL({'target': 'addons_list_marketplace', 'action': 'unset_install', 'id': arg.id}),
          function(xhr, status, data) {
          },
          {},
          {timeout: 10000}
        );
      }

      $this.removeClass('disabled')
      if (jQuery('.install-module-action:checked').length === 0) {
        $this.addClass('disabled');
      }

      return false;
    }
  );

  jQuery('.sticky-panel .additional-buttons .toggle-list-action').bind(
    'select-to-install-module',
    function (event, arg) {
      var $this = jQuery(this);

      $this.removeClass('disabled')
      if (jQuery('.install-module-action:checked').length === 0) {
        $this.addClass('disabled');
      }
    }
  );

  jQuery('.sticky-panel .modules-selected-box').bind(
    'select-to-install-module',
    function (event, arg) {
      event.stopImmediatePropagation();
      var $this = jQuery(this);

      if (arg.checked) {
        var clone = jQuery('.module-box.clone').clone().attr("id", "module-box-" + arg.id).removeClass('clone');

        jQuery(".info", clone).html(arg.id);
        jQuery(".module-name", clone).html(arg.moduleName);
        $this.append(clone);

        jQuery('.remove-action', $this).unbind('click').bind('click', function (event, arg) {
          event.stopImmediatePropagation();
          unsetModule(jQuery('.info', this).html());
        });
      } else {
        jQuery('#module-box-' + arg.id, $this).remove();
      }

      jQuery('.sticky-panel .modules-amount').html(
        jQuery('.module-box', $this).length - 1
      );

      if (jQuery('.module-box', $this).length > 1) {
        $this.removeClass('hide-selected');
        jQuery('.modules-not-selected', $this.parent()).addClass('hide-selected');
      } else {
        $this.addClass('hide-selected');
        jQuery('.modules-not-selected', $this.parent()).removeClass('hide-selected');
      }

      return false;
    }
  );

  jQuery('.sticky-panel .remove-action').bind(
    'click',
    function (event, arg) {
      unsetModule(jQuery('.info', this).html());
    }
  );

  jQuery('.form-external-link').click(
    function() {
      var url = jQuery(this).attr('href');
      var storeURL = jQuery(this).attr('data-store-url');
      var email = jQuery(this).attr('data-email');

      if (url) {
        var html = '<form action="'+url+'" target="_blank" method="post">';

        if (storeURL) {
          html = html + '<input type="hidden" name="store_url" value="'+storeURL+'" />';
        }

        if (email) {
          html = html + '<input type="hidde" name="email" value="'+email+'" />';
        }

        html = html + '</form>';

        jQuery(html).appendTo('body').submit();
      }

      return false;
    }
  );
});
