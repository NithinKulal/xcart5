/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Country selector controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function StateSelector(countrySelectorId, stateSelectorId, stateInputId)
{
  var o = this;

  if (stateSelectorId) {
    this.stateSelectBox = jQuery('#' + stateSelectorId);

    if (this.stateSelectBox.length) {
      this.stateSelectBoxValue = this.stateSelectBox.val();

      this.stateSelectBox.change(
        function(event) {
          return o.changeState(this.value);
        }
      );

      this.stateSelectBox.getParentBlock = function() {
        return o.getParentBlock(this);
      }
    }
  }

  if (stateInputId) {
    this.stateInputBox = jQuery('#' + stateInputId);

    if (this.stateInputBox.length) {
      this.stateInputBoxValue = this.stateInputBox.val();

      this.stateInputBox.getParentBlock = function() {
        return o.getParentBlock(this);
      }
    }
  }

  if (countrySelectorId) {
    this.countrySelectBox = jQuery('#' + countrySelectorId);

    if (this.countrySelectBox.length) {
      this.countrySelectBox.change(
        function(event) {
          return o.changeCountry(this.value);
        }
      );

      this.countrySelectBox.change();
    }
  }
}

StateSelector.prototype.countrySelectBox = null;
StateSelector.prototype.stateSelectBox = null;
StateSelector.prototype.stateInputBox = null;
StateSelector.prototype.stateSavedValue = null;

StateSelector.prototype.getParentBlock = function(selector)
{
  var block = selector.closest('li');

  if (!block.length) {
    block = selector.closest('div');
  }

  return block;
}

StateSelector.prototype.changeState = function(state)
{
  if (this.stateInputBox) {
    if (-1 == state) {
      this.stateInputBox.getParentBlock().show();
    } else {
      this.stateInputBox.getParentBlock().hide();
    }
  }
}

StateSelector.prototype.changeCountry = function(country)
{
  if (this.stateSelectBox) {
    if (!country) {
      this.removeOptions();
      this.stateSelectBox.getParentBlock().hide();
      this.stateInputBox.getParentBlock().hide();

    } else if (statesList[country]) {
      this.removeOptions();
      this.addStates(statesList[country]);

      this.stateSelectBox.getParentBlock().show();
      this.stateSelectBox.change();

    } else {
      this.stateSelectBox.getParentBlock().hide();
      this.stateInputBox.getParentBlock().show();
    }
  }
}

StateSelector.prototype.removeOptions = function()
{
  if (this.stateSelectBox) {
    var s = this.stateSelectBox.get(0);

    if (this.stateSelectBox.val()) {
      this.stateSavedValue = this.stateSelectBox.val();
    }

    jQuery(s).find('optgroup').remove();
    for (var i = s.options.length - 1; i >= 0; i--) {
      s.options[i] = null;
    }
  }
}

StateSelector.prototype.addDefaultOptions = function()
{
  if (this.stateSelectBox) {
    this.stateSelectBox.get(0).options[0] = new Option(core.t('Select one'), '');
    // this.stateSelectBox.get(0).options[1] = new Option('Other', '-1');
  }
}

StateSelector.prototype.addStates = function(states)
{
  if (this.stateSelectBox) {
    this.addDefaultOptions();

    var s = this.stateSelectBox.get(0);
    var added = s.options.length;
    var i = 0;

    if (states) {
        for (var id in states) {
            if (!states[id].label) {
              s.options[i + added] = new Option(states[id].name, states[id].key);
            }else{
                var optgroupValues = states[id];
                var optgroup = jQuery('<optgroup/>');
                optgroup.attr('label', optgroupValues['label']);
                for (var i = 0; i < optgroupValues['options'].length; i++) {
                  optgroup.append('<option value="' + optgroupValues['options'][i].key + '">' + optgroupValues['options'][i].name + '</option>');
                };
                jQuery(s).append(optgroup)
            };
            i++;
        }
    }

    if (this.stateSavedValue) {
      this.stateSelectBox.val(this.stateSavedValue);
    }
  }
}

jQuery(document).ready(function () {
  UpdateStatesList();
});
