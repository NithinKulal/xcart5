/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Modules list controller (manage)
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
  function () {
    ModulesItemsListQueue();
  }
);

function ModulesItemsListQueue()
{
  jQuery('.widget.items-list').each(function(index, elem){
    new ModulesItemsList(jQuery(elem));
  });
}

// Modules items list class
function ModulesItemsList(elem, urlparams, urlajaxparams)
{
  this.initialize(elem, urlparams, urlajaxparams);
}

extend(ModulesItemsList, ItemsList);

ModulesItemsList.prototype.initialize = function(elem, urlparams, urlajaxparams)
{
  var result = ItemsList.prototype.initialize.apply(this, arguments);

  if (result) {
    uninstallModules = [];
    updateUninstallButtons();
  }
}

ModulesItemsList.prototype.listeners.pagesCount = function(handler)
{
  jQuery('select.page-length', handler.container).change(
    function() {
      return !handler.process('itemsPerPage', this.options[this.selectedIndex].value);
    }
  );
};

ModulesItemsList.prototype.listeners.submitForm = function(handler)
{
  jQuery('.sticky-panel .save button[type="submit"]').click(
    function () {
      var msg = '';
      if (0 < deleteModules.length) {
        msg = confirmNote('delete');
      }

      var enableDependent = enableDependentModules();
      if (0 < enableDependent.length) {
        if (0 < msg.length) {
          msg = msg + "\n\n";
        }
        msg = msg + confirmNote('enableDependent', enableDependent);
      }

      if (msg) {
        return confirm(msg);

      } else {
        return true;
      }
    }
  );
}


// Get list of modules (array of IDs) which will be enabled because of dependency
function enableDependentModules()
{
  var result = [];

  jQuery('.module-main-section .actions .main-action .enable input[type="checkbox"]').each(
    function (index, elem) {
      var name = jQuery(elem).parents('tr.module-item').eq(0).find('.module-main-section a.module-name').eq(0).text();
      var re = /^.* module-(\d+) .*$/;
      var moduleId = jQuery(elem).parents('tr.module-item').attr('class').replace(re, "$1");

      if (jQuery(elem).is(':checked')) {
        if (0 < depends[moduleId].length && !uninstallModules[moduleId]) {
          var arr = depends[moduleId];
          for (var i = 0; i < arr.length; i++) {
            if (1 != moduleStatuses[arr[i]]) {
              result.unshift(arr[i]);
            }
          }
        }
      }
    }
  );

  return result;
}

// Global scope array: modules names selected for uninstalling
var deleteModules = new Array();

// Global function: update form comments
function updateFormComments() {
  var container = jQuery('.sticky-panel .manage-modules-comments').eq(0);
  var box = jQuery('.module-names', container).eq(0);

  deleteModules = [];

  if (0 < uninstallModules.length) {
    for (i in uninstallModules) {
      if (1 == uninstallModules[i] && moduleNames[i]) {
        deleteModules.unshift(moduleNames[i]);
      }
    }
  }

  if (deleteModules.length) {
    var content = '';
    if (4 < deleteModules.length) {
      content = core.t('X modules selected', {'count': deleteModules.length});
    } else {
      content = deleteModules.slice(0,4).join(', ');
    }

    jQuery(box).html(content);
    if (!jQuery(container).hasClass('visible')) {
      jQuery(container).addClass('visible');
    }

  } else {
    jQuery(box).html('');
    if (jQuery(container).hasClass('visible')) {
      jQuery(container).removeClass('visible');
    }
  }
}

// Add processor for click on 'Uninstall module' button
CommonForm.elementControllers.push(
{
  pattern: '.modules-list .line .actions .remove-wrapper',
  handler: function () {

    jQuery('button.remove', this).click(
      function () {
        var name = jQuery(this).parents('tr.module-item').eq(0).find('.module-main-section a.module-name').eq(0).text();
        var inp = jQuery(this).parents('.remove-wrapper').eq(0).find('input');
        var re = /^.* module-(\d+) .*$/;
        var moduleId = jQuery(this).parents('tr.module-item').attr('class').replace(re, "$1");

        if (inp.is(':checked')) {
          uninstallModules[moduleId] = 1;

        } else {
          if (uninstallModules[moduleId]) {
            uninstallModules[moduleId] = 0;
          }
        }

        updateUninstallButtons();
        updateEnableButtons();
        updateFormComments();
      }
    );
  }
}
);

function updateUninstallButtons()
{
  jQuery('.widget.items-list tr.module-item').each(function(index, elem){
    var re = /^.* module-(\d+) .*$/;
    var moduleId = jQuery(elem).attr('class').replace(re, "$1");
    var found = false;

    if (0 < dependents[moduleId].length) {
      var arr = dependents[moduleId];
      for (var i = 0; i < arr.length; i++) {
        if (1 != uninstallModules[arr[i]]) {
          found = true;
          break;
        }
      }
    }

    if (0 == uninstallModules[moduleId]) {
      if (0 < depends[moduleId].length) {
        var arr = depends[moduleId];
        for (var i = 0; i < arr.length; i++) {
          if (1 == uninstallModules[arr[i]]) {
            var box = jQuery('.widget.items-list tr.module-item.module-' + arr[i] + ' .module-main-section .actions .uninstall-action');
            if (box.length) {
              jQuery('button.remove', box).trigger('click');
            }
          }
        }
      }
    }

    var box2 = jQuery('.module-main-section .actions .uninstall-action', elem);
    if (box2.length) {
      if (found) {
        jQuery(box2).hide();
      } else {
        jQuery(box2).show();
      }
    }

  });
}


// Add processor for 'Enable module' checkox
CommonForm.elementControllers.push(
{
  pattern: '.modules-list .line .actions .main-action',
  handler: function () {

    jQuery('.disable input[type="checkbox"]', this).click(
      function () {
        var name = jQuery(this).parents('tr.module-item').eq(0).find('.module-main-section a.module-name').eq(0).text();
        var re = /^.* module-(\d+) .*$/;
        var moduleId = jQuery(this).parents('tr.module-item').attr('class').replace(re, "$1");

        if (jQuery(this).is(':checked')) {
          moduleStatuses[moduleId] = 1;

        } else {
          moduleStatuses[moduleId] = 0;
        }

        updateEnableButtons();
      }
    );

    jQuery('.enable input[type="checkbox"]', this).click(
      function () {
        var name = jQuery(this).parents('tr.module-item').eq(0).find('.module-main-section a.module-name').eq(0).text();
        var re = /^.* module-(\d+) .*$/;
        var moduleId = jQuery(this).parents('tr.module-item').attr('class').replace(re, "$1");

        if (jQuery(this).is(':checked')) {
          moduleStatuses[moduleId] = 1;

        } else {
          moduleStatuses[moduleId] = 0;
        }
      }
    );
  }
}
);

function updateEnableButtons()
{
  jQuery('.widget.items-list tr.module-item').each(function(index, elem){

    // Get the module ID
    var re = /^.* module-(\d+) .*$/;
    var moduleId = jQuery(elem).attr('class').replace(re, "$1");

    // Found: true if current module has enabled dependend module(s)
    var found = false;

    // Scanning the dependent modules
    if (0 < dependents[moduleId].length) {
      var arr = dependents[moduleId];
      for (var i = 0; i < arr.length; i++) {
        if (1 == uninstallModules[arr[i]]) {
          // Dependent module selected to be uninstalled - it's ok
        } else if (0 != moduleStatuses[arr[i]]) {
          // Found dependent module which is enabled
          found = true;
          break;
        }
      }
    }

    // Get the input box element (checkbox)
    var box = jQuery('.module-main-section .actions .main-action .disable input[type="checkbox"]', elem);
    if (box.length) {
      if (found) {
        if (!jQuery(box).is(':disabled')) {
          // Check and disable the checkbox
          jQuery(box).attr('checked', true);
          jQuery(box).attr('disabled', true);
          jQuery(box).parents('.input-field-wrapper').addClass('read-only').removeClass('disabled').addClass('enabled');
          var newValueElem = jQuery(box).parents('.input-field-wrapper').find('input[type="hidden"].new-value');
          if (0 < newValueElem.length) {
            jQuery(newValueElem).attr('value', 1);
          }
        }
      } else if (jQuery(box).is(':disabled')){
        // Remove 'disabled' attribute of the checkbox
        jQuery(box).attr('disabled', false);
        jQuery(box).parents('.input-field-wrapper').removeClass('read-only');
        var newValueElem = jQuery(box).parents('.input-field-wrapper').find('input[type="hidden"].new-value');
        if (0 < newValueElem.length) {
          jQuery(newValueElem).attr('value', 0);
        }
      }
    }

  });
}
