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

InlineEditableController.prototype.endpoint = {
  base: 'admin.php',
  target: 'inline_editable',
  action: 'update_field'
}

InlineEditableController.prototype.entity = null;
InlineEditableController.prototype.editor = null;
InlineEditableController.prototype.changed = false;

InlineEditableController.prototype.buildEntity = function (region) {
  var selector = '[data-property=' + region + ']';
  var element = jQuery(selector).first();
  return {
    model: element.data('model'),
    identifier: element.data('identifier'),
    property: element.data('property')
  };
};

InlineEditableController.prototype.init = function () {
  this.editor = ContentTools.EditorApp.get();
  this.setEditorOptions();
  this.editor.init('[data-inline-editable]', 'data-property');
  this.editor.addEventListener('saved', _.bind(this.onEditorSave, this));
};

InlineEditableController.prototype.setEditorOptions = function () {
  ContentTools.HIGHLIGHT_HOLD_DURATION = 200;
  ContentTools.IMAGE_UPLOADER = function(dialog) {
    new EditableImageUploader(dialog);
  };
};

InlineEditableController.prototype.destroy = function () {
  this.editor.destroy();
};

InlineEditableController.prototype.reset = function () {
  this.destroy();
  this.init();
};

InlineEditableController.prototype.onEditorSave = function (event) {
  var regions = event.detail().regions;
  if (!_.isEmpty(regions)) {
    this.editor.busy(true);
    var entities = {};
    for (var key in regions) {
      var entity = this.buildEntity(key);
      entities[key] = _.extend(entity, {value: regions[key]});
    }
    this.save(entities);
  }
};

InlineEditableController.prototype.save = function (data) {
  core.post(
    this.endpoint,
    null,
    {
      changeset: data
    },
    {
      dataType: 'json',
      context: this
    }
  )
  .done(this.onSaveSuccess)
  .fail(this.onSaveFail)
  .always(this.onSaveAny);
};

InlineEditableController.prototype.onSaveSuccess = function (data) {
  core.trigger(
    'message',
    {'type': 'info', 'message': data.message}
  );
};

InlineEditableController.prototype.onSaveFail = function (xhr, status, error) {
  core.showError(
    xhr.responseJSON.message
  );
};

InlineEditableController.prototype.onSaveAny = function (data) {
  this.editor.busy(false);
};

core.autoload(InlineEditableController);

