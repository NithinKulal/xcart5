/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * EditableImageUploader controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function EditableImageUploader(dialog) {
  this.dialog = dialog;

  dialog.addEventListener('imageuploader.fileready', _.bind(this.onFileReady, this));
  dialog.addEventListener('imageuploader.cancelupload', _.bind(this.onCancelUpload, this));
  dialog.addEventListener('imageuploader.clear', _.bind(this.onClear, this));
  dialog.addEventListener('imageuploader.save', _.bind(this.onSave, this));
}

EditableImageUploader.prototype.image = null;
EditableImageUploader.prototype.dialog = null;
EditableImageUploader.prototype.xhr = null;

EditableImageUploader.prototype.uploadUrl = {
  base: 'admin.php',
  target: 'files',
  action: 'upload_from_file',
  mode: 'json',
  type: 'image'
}

EditableImageUploader.prototype.saveUrl = {
  base: 'admin.php',
  target: 'inline_editable',
  action: 'save_image'
}

EditableImageUploader.prototype.getXhr = function () {
  // fix for IE XHR
  if (window.ActiveXObject !== undefined) {
    var xhr = new window.ActiveXObject( "Microsoft.XMLHTTP" )
  } else {
    var xhr = new window.XMLHttpRequest();
    xhr.upload.addEventListener('progress', _.bind(this.onUploadProgress, this));
  }

  return xhr;
}

EditableImageUploader.prototype.onFileReady = function (event) {
  var file = event.detail().file;
  this.dialog.state('uploading');
  this.dialog.progress(0);

  // Build the form data to post to the server
  var formData = new FormData();
  formData.append('file', file);

  this.xhr = core.post(
    this.uploadUrl,
    null,
    formData,
    {
      // xhr: _.bind(this.getXhr, this),
      processData: false,
      contentType: false,
      context: this
    }
  )
  .done(this.onUploadSuccess)
  .fail(this.onActionFail)
  .always(this.onUploadAny);
}

EditableImageUploader.prototype.onUploadProgress = function (ev) {
  if (this.xhr) {
    this.dialog.progress((ev.loaded / ev.total) * 100);
  }
}

EditableImageUploader.prototype.onUploadSuccess = function (data) {
  if (this.xhr) {
    if (!_.isEmpty(data) && this.validateData(data)) {
      this.image = {
        id: data.id,
        size: [data.width, data.height],
        url: data.url
      };

      this.dialog.populate(this.image.url, this.image.size);
    } else {
      this.onActionFail();
    }
  }
}


EditableImageUploader.prototype.validateData = function (data) {
  return _(data).has('id')
      && _(data).has('width')
      && _(data).has('height')
      && _(data).has('url');
}

EditableImageUploader.prototype.onActionFail = function (xhr, status, err) {
  this.onCancelUpload();
  var message;

  try {
    message = xhr.responseJSON.message;
  } catch (e) {
    message = 'Image uploading error';
  }

  core.showError(
    message
  );
}

EditableImageUploader.prototype.onUploadAny = function () {
  this.xhr = null;
}

EditableImageUploader.prototype.onCancelUpload = function () {
  if (this.xhr) {
    this.xhr.abort();
    this.xhr = null;
  }

  // Set the dialog to empty
  this.dialog.busy(false);
  this.dialog.clear();
  this.dialog.state('empty');
}

EditableImageUploader.prototype.onClear = function () {
  this.dialog.clear();
  this.image = null;
}

EditableImageUploader.prototype.onSave = function () {
  this.dialog.busy(true);

  var formData = new FormData();
  formData.append('id', this.image.id);

  // Check if a crop region has been defined by the user
  if (this.dialog.cropRegion()) {
      formData.append('crop', this.dialog.cropRegion());
  }

  core.post(
    this.saveUrl,
    null,
    formData,
    {
      processData: false,
      contentType: false,
      context: this
    }
  )
  .done(this.onSaveSuccess)
  .fail(this.onActionFail);
};

EditableImageUploader.prototype.onSaveSuccess = function (data) {
  if (!_.isEmpty(data) && this.validateData(data)) {
    this.dialog.save(
      data.url,
      [data.width, data.height],
      {
        'data-ce-max-width': this.image.size[0]
      }
    );
  } else {
    this.onActionFail();
  }
};
