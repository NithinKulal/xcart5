/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function InlineEditorPanel()
{
  this.changeset = {};
  this.images = {};
  core.bind('inline_editor.image.inserted', _.bind(this.onImageInserted, this));
  core.bind('inline_editor.changed', _.bind(this.onChanged, this));
  core.bind('inline_editor.submit', _.bind(this.submitChanges, this));
  core.bind('inline_editor.disable', _.bind(this.disableEditor, this));

  // Preload language labels
  core.loadLanguageHash(core.getCommentedData(jQuery('#inline-editor-panel')));
}

InlineEditorPanel.prototype.endpoint = {
  base: xliteConfig.admin_script,
  target: 'inline_editable',
  action: 'update_field'
};

InlineEditorPanel.prototype.elements = {
  saveBtn: $('.inline-editor-save_button'),
  exitBtn: $('.inline-editor-exit_button')
}

InlineEditorPanel.prototype.onChanged = function (event, data) {
  this.changeset[data.fieldId] = data.change;

  if (!_.isEmpty(this.changeset)) {
    this.enableButton(this.elements.saveBtn);
  }
}

InlineEditorPanel.prototype.onImageInserted = function (event, data) {
  this.images[data.imageId] = data.imageElement[0];

  if (!_.isEmpty(this.images)) {
    this.enableButton(this.elements.saveBtn);
  }
}

InlineEditorPanel.prototype.submitChanges = function (event, data) {
  this.disableButton(this.elements.saveBtn);

  if (window.inlineEditorCautionMode && !localStorage.getItem('inline_editor_ignore_incompatible_mode')) {
    var confirmation = confirm(core.t('Changes may be incompatible with TinyMCE. Are you sure to proceed?'));

    if (confirmation) {
      localStorage.setItem('inline_editor_ignore_incompatible_mode', true);
    } else {
      return;
    }
  }

  core.post(
    this.endpoint,
    null,
    {
      changeset: this.changeset,
      images: _.keys(this.images)
    },
    {
      dataType: 'json',
      context: this,
    }
  )
  .done(_.bind(this.onSaveSuccess, this))
  .fail(_.bind(this.onSaveFail, this));
};

InlineEditorPanel.prototype.onSaveSuccess = function (event, status, xhr) {
    core.trigger('message', {type: 'info', message: core.t('Changes were successfully saved')});

    var updatedUrls = xhr.responseJSON.imageUrls;
    var self = this;

    _.each(_.keys(updatedUrls), function(imageId) {
      if (self.images[imageId]) {
        self.images[imageId].src = updatedUrls[imageId];
      }
    });

    this.changeset = {};
    this.images = {};
    this.elements.saveBtn.text(core.t('Save changes'));
};

InlineEditorPanel.prototype.onSaveFail = function (event) {
    core.trigger('message', {type: 'error', message: core.t('Unable to save changes')});

    this.elements.saveBtn.text(core.t('Try again'));
    this.enableButton(this.elements.saveBtn);
};


InlineEditorPanel.prototype.disableEditor = function (event) {
    this.disableButton(this.elements.exitBtn);
    var confirmation = true;
    if (!_.isEmpty(this.changeset)) {
        confirmation = confirm(core.t('You have unsaved changes. Are you really sure to exit the preview?'));
    }

    if (!confirmation) {
        this.enableButton(this.elements.exitBtn);
        return;
    }

    this.elements.exitBtn.text('Exiting...');

    window.location = URLHandler.buildURL({
      base: xliteConfig.admin_script,
      target: 'product',
      product_id: core.getURLParam('product_id')
    });
};

InlineEditorPanel.prototype.disableButton = function(button) {
    button.addClass('disabled');
    button.prop('disabled', true);
};

InlineEditorPanel.prototype.enableButton = function(button) {
    button.removeClass('disabled');
    button.prop('disabled', false);
};

core.autoload(InlineEditorPanel);