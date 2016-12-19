/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * file uploader controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Main class
function FileUploader (base) {
  this.commonData = jQuery(base).parent().data();
  this.commonData.target = 'files';
  this.callSupermethod('constructor', arguments);

  var multiple = jQuery('.multiple-files');
  if (multiple.length) {
    multiple.sortable({
      placeholder:          'ui-state-highlight',
      forcePlaceholderSize: false,
      distance:             10,
      items:                '> div.item',
      update:               function(event, ui)
      {
        repositionFiles(this);
      },
      activate: function(event, ui) {
        if (ui.item.hasClass('open')) {
          ui.item.find('.link').dropdown('toggle');
        };
      }
    });

    multiple.each(
      function() {
        repositionFiles(this, true);
      }
    );
  }
}

function repositionFiles (base, saveAsInitial) {
  base = jQuery(base);

  var min = 10;
  base.find('input.input-position').each(function () {
    min = parseInt(10 == min ? min : Math.min(this.value, min));
  });

  base.find('input.input-position').each(function () {
    jQuery(this).attr('value', min);
    if (saveAsInitial) {
      if (this.commonController) {
        this.commonController.saveValue();
      }

    } else {
      jQuery(this).change();
    }
    min += 10;
  });
}

extend(FileUploader, ALoadable);

FileUploader.prototype.assignWait = function () {
  this.base.html('<div class="spinner"></div>');
};

FileUploader.prototype.refresh = function () {
  var o = this;
  var formData = new FormData();
  formData.append('object_id', jQuery(o.base).data('objectId'));
  formData.append('markAsImage', true);
  o.commonData.action = 'refresh';

  this.assignWait();

  jQuery.ajax({
    url: URLHandler.buildURL(o.commonData),
    type: 'post',
    xhr: function() {
      return jQuery.ajaxSettings.xhr();
    },
    success: function (data, status, xhr) {
      o.loadHandler(xhr, status, data);
    },
    data: formData,
    cache: false,
    contentType: false,
    processData: false
  });
};

FileUploader.prototype.request = function (formData, multiple) {
  var o = this;

  formData.append('object_id', jQuery(o.base).data('objectId'));
  if (multiple) {
    o = jQuery(document.createElement('div'))
      .addClass('file-uploader')
      .addClass('dropdown')
      .insertBefore(this.base);
    o = new FileUploader(jQuery(o));
  }

  o.assignWait();

  jQuery.ajax({
    url: URLHandler.buildURL(o.commonData),
    type: 'post',
    xhr: function() {
      return jQuery.ajaxSettings.xhr();
    },
    success: function (data, status, xhr) {
      o.loadHandler(xhr, status, data);
      var multipleFiles = jQuery(o.base).parents('.multiple-files').get(0);
      if (multipleFiles) {
        repositionFiles(multipleFiles);
      }
      var form = jQuery(o.base).parents('form').get(0);
      if (form) {
        jQuery(form).addClass('changed');
        jQuery(form).trigger('state-changed');
      }
    },
    data: formData,
    cache: false,
    contentType: false,
    processData: false
  });
};

// Postprocess widget
FileUploader.prototype.postprocess = function (isSuccess) {
  if (isSuccess) {
    var o = this;

    jQuery('a.from-computer', o.base).bind(
      'click',
      function (event)
      {
        jQuery('input[type=file]', o.base).val('').click();

        return false;
      }
    );

    jQuery('div.via-url-popup button', o.base).bind(
      'click',
      function (event)
      {
        viaUrlPopup.dialog('close');
        var formData = new FormData();
        o.commonData.action = 'uploadFromURL';
        if (jQuery('input.copy-to-file', jQuery(this).parent()).prop('checked')) {
          formData.append('copy', 1);
        }
        if (viaUrlPopup.data('multiple')) {
          var area = jQuery('textarea.urls', viaUrlPopup);
          var urls = area.val().split('\n');

          urls.forEach(function (url) {
            url = url.replace(/^:?\/\//, '');

            if (!/^https?:\/\//i.test(url)) {
              url = 'http://' + url;
            }

            formData.append('uploadedUrl', url);
            o.request(formData, true);
          });

          area.val('');

        } else if (jQuery('input.url', viaUrlPopup).val()) {
          var url = jQuery('input.url', viaUrlPopup).val();
          url = url.replace(/^:?\/\//, '');

          if (!/^https?:\/\//i.test(url)) {
            url = 'http://' + url;
          }
          formData.append('uploadedUrl', url);
          o.request(formData, false);
        }
      }
    );

    jQuery('input[type=file]', o.base).bind(
      'change',
      function (event)
      {
        var formData = new FormData();
        o.commonData.action = 'uploadFromFile';
        for (var i = 0; i < this.files.length; i++) {
          formData.append('file', this.files[i]);
          o.request(formData, viaUrlPopup.data('multiple'));
        }
      }
    );

    jQuery('a.via-url', o.base).bind(
      'click',
      function (event) {
        viaUrlPopup.dialog('open');
        jQuery('.dropdown').click();

        return false;
      }
    );

    jQuery('li.alt-text .value', o.base).bind(
      'click',
      function (event) {
        jQuery(this).hide();
        jQuery('li.alt-text .input-group', o.base).css('display','table');
        jQuery('li.alt-text .input-group input', o.base).focus();

        return false;
      }
    );

    jQuery('input.input-alt', o.base).bind(
      'click',
      function (event) {
        return false;
      }
    ).bind(
      'change keydown blur',
      function (event)
      {
        if (!event.keyCode || 13 === event.keyCode) {
          jQuery(this).parent().hide();
          jQuery('li.alt-text .value span', o.base).text(jQuery(this).val());
          jQuery('li.alt-text .value', o.base).show();

          return false;
        }
      }
    );

    jQuery('a.delete', o.base).bind(
      'click',
      function (event)
      {
        if (jQuery(o.base).hasClass('remove-mark')) {
          jQuery(o.base).removeClass('remove-mark');

        } else {
          jQuery(o.base).addClass('remove-mark');
        }
        jQuery('input.input-delete', o.base).click();
        jQuery('.dropdown').click();

        return false;
      }
    );

    var viaUrlPopup = jQuery('.via-url-popup', o.base);
    viaUrlPopup = jQuery('.via-url-popup', o.base).dialog(
      {
        autoOpen:  false,
        draggable: false,
        title:     viaUrlPopup.data('title'),
        width:     500,
        modal:     true,
        resizable: false,
        open:      _.bind(
          function(event, ui) {
            jQuery('.overlay-blur-base').addClass('overlay-blur');
          },
          this
        ),
        close:     _.bind(
          function(event, ui) {
            jQuery('.overlay-blur-base').removeClass('overlay-blur');
          },
          this
        )
      }
    );
  }
};

core.microhandlers.add(
    'file-uploader',
    'div.file-uploader',
    function(event, element) {
      core.autoload(FileUploader, element);
    }
);

core.bind('list.model.table.newLineCreated', function(event, data) {
    var line = jQuery('.create-line').last();
    if (!line.length) {
      return;
    };
    var uploader = line.find('div.file-uploader');
    if (!uploader.length) {
      return;
    };

    var newUploader = uploader.clone();
    uploader.remove();
    line.find('.cell.image .table-value > div').append(newUploader);
    var controller = new FileUploader(newUploader);
    line
      .find(':input')
      .each(
        function () {
            var el = jQuery(this).parents('.table-value').children('div');

            if (el.data('name') && data.idx) {
                var newName = el.data('name').replace(/\[0\]/, '[' + (-1 * data.idx) + ']');
                el.data('name', newName);
                var newUploader = el.find('.file-uploader');
                newUploader.data('object-id', (-1 * data.idx));
            }
        }
      );
    controller.refresh();
});
