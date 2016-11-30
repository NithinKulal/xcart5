/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function LayoutEditorPanel()
{
    core.bind('layout.moved', _.bind(this.onListChange, this));
    core.bind('layout.rearranged', _.bind(this.onListChange, this));
    core.bind('layout.submit', _.bind(this.submitChanges, this));
    core.bind('layout.disable_editor', _.bind(this.disableEditor, this));
    core.bind('layout.hide', _.bind(this.onItemHide, this));
    core.bind('layout.unhide', _.bind(this.onItemUnhide, this));
    core.bind('layout.show_hidden', _.bind(this.showHidden, this));
    core.bind('layout.toggle_editor', _.bind(this.toggleEditor, this));

    var initial = $('.list-item__hidden').length + $('.list-item__temporary').length;

    this.state = localStorage.getItem('layout-editor-mode');

    $('#layout-editor-panel').removeClass('layout-editor--initial');

    if (this.state === null) {
        this.state = true;
    } else {
        this.state = (this.state === "true");
    }

    if (!this.state) {
        $('.layout-editor-toggle input[type="checkbox"]').prop("checked", false);
        this.toggleEditorPanel(this.state);
    }

    $('.layout-editor-toggle input[type="checkbox"]').change(function() {
        core.trigger('layout.toggle_editor');
    });

    this.updateHiddenItemsCounter(initial);

    // Preload language labels
    core.loadLanguageHash(core.getCommentedData(jQuery('#layout-editor-panel')));
}

LayoutEditorPanel.prototype.changeset = {};
LayoutEditorPanel.prototype.hidden = 0;

// true means Sortable is enabled and false is otherwise
LayoutEditorPanel.prototype.state = null;

LayoutEditorPanel.prototype.onListChange = function (event, data) {
    this.addChange(data.id, data.list, data.position, 1);
};

LayoutEditorPanel.prototype.onItemHide = function (event, sender) {
    var item = $(sender).closest('.list-item');
    var list = item.closest('.list-items-group');
    item.listItem('hide');
    this.updateHiddenItemsCounter(1);
    this.addChange(item.data('id'), list.data('list'), item.data('weight'), 2);
};

LayoutEditorPanel.prototype.onItemUnhide = function (event, sender) {
    var item = $(sender).closest('.list-item');
    var list = item.closest('.list-items-group');
    item.removeClass('list-item__temporary');
    this.updateHiddenItemsCounter(-1);
    this.addChange(item.data('id'), list.data('list'), item.data('weight'), 1);
};

LayoutEditorPanel.prototype.updateHiddenItemsCounter = function(modifier) {
    modifier = modifier || 0;
    this.hidden += modifier;
    $('.layout-editor-show_blocks-counter').text(this.hidden);

    if (this.hidden === 0) {
        $('.layout-editor-show_button').addClass('layout-editor-show_button__hidden');
    } else {
        $('.layout-editor-show_button').removeClass('layout-editor-show_button__hidden');
    }
};

LayoutEditorPanel.prototype.showHidden = function (event, sender) {
    $('.list-item__hidden').addClass('list-item__temporary').listItem('show');
};

LayoutEditorPanel.prototype.addChange = function (id, list, weight, mode) {
    this.changeset[id] = {
        id: id,
        list: list,
        weight: weight,
        mode: mode,
    };

    var saveBtn = $('.layout-editor-save_button');
    this.enableButton(saveBtn);
};

LayoutEditorPanel.prototype.submitChanges = function (event) {
    var saveBtn = $('.layout-editor-save_button');
    this.disableButton(saveBtn);

    core.post(
      {
        base: 'admin.php',
        target: 'layout_edit',
        action: 'apply_changes'
      },
      null,
      {
        'changes': this.changeset
      },
      {
        success: _.bind(this.onSaveSuccess, this),
        fail: _.bind(this.onSaveFail, this),
      }
    );
};

LayoutEditorPanel.prototype.onSaveSuccess = function (event) {
    core.trigger('message', {type: 'info', message: core.t('Changes were successfully saved')});
    this.changeset = {};
};

LayoutEditorPanel.prototype.onSaveFail = function (event) {
    core.trigger('message', {type: 'error', message: core.t('Unable to save changes')});

    var saveBtn = $('.layout-editor-save_button');
    saveBtn.text(core.t('Try again'));
    this.enableButton(saveBtn);
};

LayoutEditorPanel.prototype.toggleEditor = function (event) {
    $('.list-container').each(function() {
        $(this).data('controller').toggle();
    });

    this.state = !this.state;

    this.toggleEditorPanel(this.state);

    localStorage.setItem('layout-editor-mode', this.state);
};


LayoutEditorPanel.prototype.toggleEditorPanel = function (state) {
    $('.layout-editor-toggle').toggleClass('layout-editor--disabled', !state);
    if (state) {
        $('.layout-editor--switchable').show();
    } else {
        $('.layout-editor--switchable').hide();
    }
}

LayoutEditorPanel.prototype.disableEditor = function (event) {
    var exitBtn = $('.layout-editor-exit_button');
    this.disableButton(exitBtn);
    var confirmation = true;
    if (!_.isEmpty(this.changeset)) {
        confirmation = confirm(core.t('You have unsaved changes. Are you really sure to exit the layout editor?'));
    }

    if (!confirmation) {
        this.enableButton(exitBtn);
        return;
    }

    exitBtn.text('Exiting...');

    core.post(
      {
        base: 'admin.php',
        target: 'layout_edit',
        action: 'disable'
      },
      null,
      {
        'returnURL': window.location.href
      }
    );
};

LayoutEditorPanel.prototype.disableButton = function(button) {
    button.addClass('disabled');
    button.prop('disabled', true);
};

LayoutEditorPanel.prototype.enableButton = function(button) {
    button.removeClass('disabled');
    button.prop('disabled', false);
};

core.autoload(LayoutEditorPanel);