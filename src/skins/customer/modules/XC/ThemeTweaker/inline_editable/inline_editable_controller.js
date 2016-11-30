/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Reloadable layout block widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function InlineEditableController()
{
  this.init();

  core.bind('loader.loaded', _.bind(this.reset, this));
}

InlineEditableController.prototype.selector = '[data-inline-editable]';

InlineEditableController.prototype.imageManagerLoadURL = URLHandler.buildURL({
  base: 'admin.php',
  target: 'files',
  action: 'get_image_manager_list',
});

InlineEditableController.prototype.imageManagerDeleteURL = URLHandler.buildURL({
  base: 'admin.php',
  target: 'files',
  action: 'remove_from_image_manager',
});

InlineEditableController.prototype.imageUploadURL = URLHandler.buildURL({
  base: 'admin.php',
  target: 'files',
  action: 'upload_from_file',
  mode: 'json',
  type: 'image'
});

InlineEditableController.prototype.buildChangeRecord = function (element) {
  element = $(element);
  return {
    model: element.data('model'),
    identifier: element.data('identifier'),
    property: element.data('property'),
    value: element.froalaEditor('html.get')
  };
};

InlineEditableController.prototype.init = function () {
  $(this.selector).froalaEditor(this.getEditorOptions());
  $(this.selector).on('froalaEditor.contentChanged', _.bind(this.onContentChanged, this));
  $(this.selector).on('froalaEditor.image.inserted froalaEditor.image.replaced', _.bind(this.onImageInserted, this));
};

InlineEditableController.prototype.getEditorOptions = function () {
  return {
    toolbarInline: true,
    toolbarVisibleWithoutSelection: true,
    charCounterCount: false,
    imageUploadURL: this.imageUploadURL,
    imageManagerLoadURL: this.imageManagerLoadURL,
    imageManagerDeleteURL: this.imageManagerDeleteURL,
    imageUploadParam: 'file',
    imageUploadParams: {
      url_param_name: 'link'
    },
    zIndex: 9990,
    requestHeaders: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    toolbarButtons: ['fontFamily', 'fontSize', '|', 'bold', 'italic', 'underline', 'strikeThrough', 'color', '-', 'paragraphFormat', 'paragraphStyle', 'align', 'formatOL', 'formatUL', '|', 'indent', 'outdent', '-', 'insertImage', 'insertTable', 'insertLink', 'insertVideo', '|', 'undo', 'redo', 'html']
  };
};

InlineEditableController.prototype.destroy = function () {
  if ($(this.selector).data('froala.editor')) {
    $(this.selector).froalaEditor('destroy');
  }
};

InlineEditableController.prototype.reset = function () {
  this.destroy();
  this.init();
};

InlineEditableController.prototype.onContentChanged = function (event, editor) {
  core.trigger('inline_editor.changed', {
    event: event,
    sender: this,
    change: this.buildChangeRecord(event.currentTarget),
    fieldId: $(event.currentTarget).data('property')
  });
};

InlineEditableController.prototype.onImageInserted = function (event, editor, element, response) {
  core.trigger('inline_editor.image.inserted', {
    event: event,
    sender: this,
    imageId: JSON.parse(response).id,
    imageElement: element
  });
};

InlineEditableController.prototype.getFullChangeset = function() {
  var changeset = {};
  if ($(this.selector).data('froala.editor')) {
    $(this.selector).each(function() {
      changeset[$(this).data('property')] = $(this).froalaEditor('html.get');
    })
  }

  return changeset;
}

core.autoload(InlineEditableController);

